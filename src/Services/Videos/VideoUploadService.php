<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:50
 */

namespace Jinya\Services\Videos;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use Exception;
use Jinya\Entity\Video\UploadingVideo;
use Jinya\Entity\Video\UploadingVideoChunk;
use Jinya\Entity\Video\Video;
use Jinya\Framework\Events\Videos\VideoUploadChunkEvent;
use Jinya\Framework\Events\Videos\VideoUploadCleanupAfterUploadEvent;
use Jinya\Framework\Events\Videos\VideoUploadFinishUploadEvent;
use Jinya\Framework\Events\Videos\VideoUploadStartUploadEvent;
use Jinya\Services\Media\MediaServiceInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use /** @noinspection PhpUndefinedClassInspection */
    Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * VideoUploadService constructor.
     * @param VideoServiceInterface $videoService
     * @param MediaServiceInterface $mediaService
     * @param string $tmpDir
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        VideoServiceInterface $videoService,
        MediaServiceInterface $mediaService,
        string $tmpDir,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->videoService = $videoService;
        $this->mediaService = $mediaService;
        $this->tmpDir = $tmpDir;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Starts the upload
     *
     * @param string $slug
     */
    public function startUpload(string $slug): void
    {
        $video = $this->videoService->get($slug);
        $pre = $this->eventDispatcher->dispatch(
            VideoUploadStartUploadEvent::PRE_START_UPLOAD,
            new VideoUploadStartUploadEvent($video)
        );

        if (!$pre->isCancel()) {
            $uploadingVideo = new UploadingVideo();
            $uploadingVideo->setVideo($video);

            $this->entityManager->persist($uploadingVideo);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                VideoUploadStartUploadEvent::POST_START_UPLOAD,
                new VideoUploadStartUploadEvent($video)
            );
        }
    }

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     * @param int $position
     * @param string $slug
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function uploadChunk($chunk, int $position, string $slug): void
    {
        $pre = $this->eventDispatcher->dispatch(
            VideoUploadChunkEvent::PRE_UPLOAD_CHUNK,
            new VideoUploadChunkEvent($chunk, $position, $slug)
        );

        if (!$pre->isCancel()) {
            $uploadingVideo = $this->getUploadingVideo($slug);
            $chunkDirectory = $this->tmpDir;
            $chunkPath = $chunkDirectory . DIRECTORY_SEPARATOR . uniqid($slug, true);

            if (!mkdir($chunkDirectory) && !is_dir($chunkDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $chunkDirectory));
            }
            file_put_contents($chunkPath, $chunk);

            $uploadingVideoChunk = new UploadingVideoChunk();
            $uploadingVideoChunk->setUploadingVideo($uploadingVideo);
            $uploadingVideoChunk->setChunkPosition($position);
            $uploadingVideoChunk->setChunkPath($chunkPath);

            $this->entityManager->persist($uploadingVideoChunk);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                VideoUploadChunkEvent::PRE_UPLOAD_CHUNK,
                new VideoUploadChunkEvent($chunk, $position, $slug)
            );
        }
    }

    /**
     * @param string $slug
     * @return UploadingVideo
     * @throws NoResultException
     * @throws NonUniqueResultException
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

        $pre = $this->eventDispatcher->dispatch(
            VideoUploadFinishUploadEvent::PRE_FINISH_UPLOAD,
            new VideoUploadFinishUploadEvent($chunks, $slug)
        );
        if (empty($pre->getPath())) {
            $newFile = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid('last-chunk', true);
            $newFileHandle = fopen($newFile, 'ab');

            try {
                /** @var UploadingVideoChunk $chunk */
                foreach ($chunks as $chunk) {
                    $chunkFileHandle = fopen($chunk->getChunkPath(), 'rb');

                    try {
                        if (filesize($chunk->getChunkPath()) > 0) {
                            $chunkData = fread($chunkFileHandle, filesize($chunk->getChunkPath()));
                            fwrite($newFileHandle, $chunkData);
                        }
                    } finally {
                        fclose($chunkFileHandle);
                    }
                }
            } finally {
                fclose($newFileHandle);
            }

            $path = $this->mediaService->moveMedia($newFile, MediaServiceInterface::VIDEO_VIDEO);
        } else {
            $path = $pre->getPath();
        }

        $event = new VideoUploadFinishUploadEvent($chunks, $slug);
        $event->setPath($path);
        $this->eventDispatcher->dispatch(VideoUploadFinishUploadEvent::POST_FINISH_UPLOAD, $event);

        return $path;
    }

    /**
     * @param string $slug
     * @return UploadingVideoChunk[]
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function cleanupAfterUpload(string $slug): void
    {
        $chunks = $this->getChunks($slug);

        $pre = $this->eventDispatcher->dispatch(
            VideoUploadCleanupAfterUploadEvent::PRE_CLEANUP_AFTER_UPLOAD,
            new VideoUploadCleanupAfterUploadEvent($slug, $chunks)
        );
        if (!$pre->isCancel()) {
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
            $this->eventDispatcher->dispatch(
                VideoUploadCleanupAfterUploadEvent::POST_CLEANUP_AFTER_UPLOAD,
                new VideoUploadCleanupAfterUploadEvent($slug, $chunks)
            );
        }
    }
}
