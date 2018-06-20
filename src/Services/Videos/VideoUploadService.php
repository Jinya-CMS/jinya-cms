<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:50
 */

namespace Jinya\Services\Videos;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Exception;
use Jinya\Entity\Video\UploadingVideo;
use Jinya\Entity\Video\UploadingVideoChunk;
use Jinya\Entity\Video\Video;
use Jinya\Services\Media\MediaServiceInterface;
use Psr\Log\LoggerInterface;

class VideoUploadService implements VideoUploadServiceInterface
{
    /** @var VideoServiceInterface */
    private $videoService;

    /** @var MediaServiceInterface */
    private $mediaService;

    /** @var string */
    private $tmpDir;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * VideoUploadService constructor.
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @param string $tmpDir
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(VideoServiceInterface $videoService, MediaServiceInterface $mediaService, string $tmpDir, EntityManagerInterface $entityManager)
    {
        $this->videoService = $videoService;
        $this->mediaService = $mediaService;
        $this->tmpDir = $tmpDir;
        $this->entityManager = $entityManager;
    }

    /**
     * Starts the upload
     *
     * @param string $slug
     */
    public function startUpload(string $slug): void
    {
        $video = $this->videoService->get($slug);

        $uploadingVideo = new UploadingVideo();
        $uploadingVideo->setVideo($video);

        $this->entityManager->persist($uploadingVideo);
        $this->entityManager->flush();
    }

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     * @param int $position
     * @param string $slug
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function uploadChunk($chunk, int $position, string $slug): void
    {
        $uploadingVideo = $this->getUploadingVideo($slug);
        $chunkDirectory = $this->tmpDir;
        $chunkPath = $chunkDirectory . DIRECTORY_SEPARATOR . uniqid($slug);

        @mkdir($chunkDirectory);
        file_put_contents($chunkPath, $chunk);

        $chunk = new UploadingVideoChunk();
        $chunk->setUploadingVideo($uploadingVideo);
        $chunk->setChunkPosition($position);
        $chunk->setChunkPath($chunkPath);

        $this->entityManager->persist($chunk);
        $this->entityManager->flush();
    }

    /**
     * @param string $slug
     * @return UploadingVideo
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getUploadingVideo(string $slug): UploadingVideo
    {
        return $this->entityManager->createQueryBuilder()
            ->select('uv')
            ->from(UploadingVideo::class, 'uv')
            ->join(Video::class, 'video', Expr\Join::WITH, 'video = uv.video')
            ->where('video.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Finishes the upload
     *
     * @param string $slug
     * @return string
     * @throws Exception
     */
    public function finishUpload(string $slug): string
    {
        $chunks = $this->getChunks($slug);

        $newFile = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid();
        $newFileHandle = fopen($newFile, 'a');

        try {
            /** @var UploadingVideoChunk $chunk */
            foreach ($chunks as $chunk) {
                $chunkFileHandle = fopen($chunk->getChunkPath(), 'rb');

                try {
                    $chunkData = fread($chunkFileHandle, filesize($chunk->getChunkPath()));
                    fwrite($newFileHandle, $chunkData);
                } finally {
                    fclose($chunkFileHandle);
                }
            }
        } catch (Exception $exception) {
            throw $exception;
        } finally {
            fclose($newFileHandle);
        }

        return $this->mediaService->moveMedia($newFile, MediaServiceInterface::VIDEO_VIDEO);
    }

    /**
     * @param string $slug
     * @return array
     */
    private function getChunks(string $slug): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('uvc')
            ->from(UploadingVideoChunk::class, 'uvc')
            ->join(UploadingVideo::class, 'uv')
            ->join(Video::class, 'video')
            ->where('video.slug = :slug')
            ->orderBy('uvc.chunkPosition')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all chunk data after upload
     *
     * @param string $slug
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function cleanupAfterUpload(string $slug): void
    {
        $chunks = $this->getChunks($slug);

        /** @var UploadingVideoChunk $chunk */
        foreach ($chunks as $chunk) {
            try {
                @unlink($chunk->getChunkPath());
            } catch (Exception $exception) {
                $this->logger->warning("Couldn't unlink chunk " . $chunk->getChunkPath());
            }
        }

        $uploadingVideo = $this->getUploadingVideo($slug);
        $this->entityManager->remove($uploadingVideo);
        $this->entityManager->flush();
    }
}
