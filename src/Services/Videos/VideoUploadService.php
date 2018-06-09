<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:50
 */

namespace Jinya\Services\Videos;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Video\UploadingVideo;
use Jinya\Entity\Video\UploadingVideoChunk;
use Jinya\Services\Media\MediaServiceInterface;

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
        $chunkDirectory = $this->tmpDir . DIRECTORY_SEPARATOR . 'uploading_video' . DIRECTORY_SEPARATOR;

        @mkdir($chunkDirectory);
        file_put_contents($chunkDirectory . uniqid($slug), $chunk);

        $chunk = new UploadingVideoChunk();
        $chunk->setUploadingVideo($uploadingVideo);
        $chunk->setChunkPosition($position);

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
            ->where('uv.video.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Finishes the upload
     *
     * @param string $slug
     * @return string
     */
    public function finishUpload(string $slug): string
    {
        return '';
    }
}