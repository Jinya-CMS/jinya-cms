<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 08.06.18
 * Time: 18:50
 */

namespace Jinya\Services\Media\Upload;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Jinya\Entity\Video\UploadingFile;
use Jinya\Entity\Video\UploadingFileChunk;
use Jinya\Entity\Video\UploadingVideoChunk;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class FileUploadService implements FileUploadServiceInterface
{
    /** @var FileServiceInterface */
    private $fileService;

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
     * @param FileServiceInterface $fileService
     * @param MediaServiceInterface $mediaService
     * @param string $tmpDir
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        FileServiceInterface $fileService,
        MediaServiceInterface $mediaService,
        string $tmpDir,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->fileService = $fileService;
        $this->mediaService = $mediaService;
        $this->tmpDir = $tmpDir;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Starts the upload
     *
     * @param int $id
     */
    public function startUpload(int $id): void
    {
        $file = $this->fileService->get($id);
        $uploadingFile = new UploadingFile();
        $uploadingFile->setFile($file);

        $this->entityManager->persist($uploadingFile);
        $this->entityManager->flush();
    }

    /**
     * Uploads a chunk
     *
     * @param resource $chunk
     * @param int $position
     * @param int $id
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function uploadChunk($chunk, int $position, int $id): void
    {
        $uploadingFile = $this->getUploadingFile($id);
        $chunkDirectory = $this->tmpDir;
        $chunkPath = $chunkDirectory . DIRECTORY_SEPARATOR . uniqid($id, true);

        if (!mkdir($chunkDirectory) && !is_dir($chunkDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $chunkDirectory));
        }
        file_put_contents($chunkPath, $chunk);

        $uploadingFileChunk = new UploadingFileChunk();
        $uploadingFileChunk->setUploadingFile($uploadingFile);
        $uploadingFileChunk->setChunkPosition($position);
        $uploadingFileChunk->setChunkPath($chunkPath);

        $this->entityManager->persist($uploadingFileChunk);
        $this->entityManager->flush();
    }

    /**
     * @param string $id
     * @return UploadingFile
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function getUploadingFile(int $id): UploadingFile
    {
        return $this->entityManager->createQueryBuilder()
            ->select('uploading_file')
            ->from(UploadingFile::class, 'uploading_file')
            ->join('uploading_file.file', 'file')
            ->where('file.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Finishes the upload
     *
     * @param int $id
     * @return string
     */
    public function finishUpload(int $id): string
    {
        $chunks = $this->getChunks($id);

        $newFile = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid('last-chunk', true);
        $newFileHandle = fopen($newFile, 'ab');

        try {
            /** @var UploadingFileChunk $chunk */
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

        return $this->mediaService->moveMedia($newFile, MediaServiceInterface::JINYA_CONTENT);
    }

    /**
     * @param int $id
     * @return UploadingVideoChunk[]
     */
    private function getChunks(int $id): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('uvc')
            ->from(UploadingFileChunk::class, 'uploading_file_chunk')
            ->join('uploading_file_chunk.uploadingFile', 'uploading_file')
            ->join('uploading_file.file', 'file')
            ->where('file.id = :id')
            ->orderBy('uploading_file_chunk.chunkPosition')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all chunk data after upload
     *
     * @param int $id
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function cleanupAfterUpload(int $id): void
    {
        $chunks = $this->getChunks($id);

        foreach ($chunks as $chunk) {
            try {
                @unlink($chunk->getChunkPath());
            } catch (Exception $exception) {
                $this->logger->warning("Couldn't unlink chunk " . $chunk->getChunkPath());
            }
        }

        $uploadingVideo = $this->getUploadingFile($id);
        $this->entityManager->remove($uploadingVideo);
        $this->entityManager->flush();
    }
}
