<?php

namespace Jinya\Cms\Storage;

use Exception;
use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\UploadingFile;
use Jinya\Cms\Database\UploadingFileChunk;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Utils\UuidGenerator;
use Jinya\Database\Exception\NotNullViolationException;
use RuntimeException;
use Throwable;

/**
 * A simple helper to handle file uploads
 */
class FileUploadService extends StorageBaseService
{
    public function __construct(private readonly ConversionService $conversionService = new ConversionService())
    {
        parent::__construct();
        $this->logger = Logger::getLogger();
    }

    /**
     * Saves a new file chunk and returns it
     *
     * @param int $fileId
     * @param int $position
     * @param string|resource|null $data
     * @return UploadingFileChunk
     * @throws EmptyResultException
     * @throws NotNullViolationException
     * @throws Exception
     */
    public function saveChunk(int $fileId, int $position, mixed $data): UploadingFileChunk
    {
        $this->logger->debug('Save new chunk for file', ['fileId' => $fileId, 'position' => $position]);
        if ($data === null) {
            $this->logger->error(
                'Passed data is null, this is unsupported',
                ['fileId' => $fileId, 'position' => $position]
            );
            throw new RuntimeException();
        }

        $this->logger->debug(
            'Store data in temporary file',
            ['fileId' => $fileId, 'position' => $position]
        );
        $path = __JINYA_TEMP . UuidGenerator::generateV4();
        file_put_contents($path, $data);

        $this->logger->debug(
            'Get uploading file from database for file',
            ['fileId' => $fileId]
        );
        $uploadingFile = UploadingFile::findByFile($fileId);
        if ($uploadingFile === null) {
            $this->logger->warning(
                'Uploading file not found, upload was not started',
                ['fileId' => $fileId]
            );
            throw new EmptyResultException('File not found');
        }

        $chunk = new UploadingFileChunk();
        $chunk->chunkPath = $path;
        $chunk->chunkPosition = $position;
        $chunk->uploadingFileId = $uploadingFile->id;

        $this->logger->debug(
            'Store chunk in database',
            ['fileId' => $fileId, 'position' => $position]
        );
        $chunk->create();

        return $chunk;
    }

    /**
     * Finishes the upload for the given file
     *
     * @param int $fileId
     * @return null|object
     * @throws EmptyResultException
     * @throws NotNullViolationException
     * @throws Exception
     */
    public function finishUpload(int $fileId): object|null
    {
        $this->logger->debug('Finish upload for file', ['fileId' => $fileId]);
        $file = File::findById($fileId);
        if ($file === null) {
            $this->logger->warning('File does not exist', ['fileId' => $fileId]);
            throw new EmptyResultException('File not found');
        }

        $this->logger->debug('Get all chunks', ['fileId' => $fileId]);
        $chunks = UploadingFileChunk::findByFile($fileId);

        if (!@mkdir(self::SAVE_PATH, 0775, true) && !@is_dir(self::SAVE_PATH)) {
            $this->logger->error('Failed to create save directory', ['directory' => self::SAVE_PATH]);
            throw new RuntimeException(sprintf('Directory "%s" was not created', self::SAVE_PATH));
        }

        $this->logger->debug('Create temporary file for saving', ['fileId' => $fileId]);
        $tmpFileHandle = tmpfile();
        if (!is_resource($tmpFileHandle)) {
            $this->logger->warning('Could not open the temporary file', ['fileId' => $fileId]);
            return null;
        }

        try {
            $this->logger->debug('Store all chunks in the temporary file', ['fileId' => $fileId]);
            foreach ($chunks as $chunk) {
                $chunkFileHandle = fopen($chunk->chunkPath, 'rb');

                if (is_resource($chunkFileHandle)) {
                    try {
                        if (filesize($chunk->chunkPath) > 0) {
                            $chunkData = fread($chunkFileHandle, filesize($chunk->chunkPath));
                            if (is_string($chunkData)) {
                                $this->logger->debug(
                                    'Append chunk to the temporary file',
                                    ['fileId' => $fileId, 'chunk' => $chunk->chunkPosition]
                                );
                                fwrite($tmpFileHandle, $chunkData);
                            }
                        }
                    } finally {
                        @fclose($chunkFileHandle);
                    }
                }
            }

            $fileName = $this->getFileHash($tmpFileHandle);
            $path = self::SAVE_PATH . $fileName;

            $this->logger->debug('Store the complete file in public folder', ['fileId' => $fileId]);
            rewind($tmpFileHandle);
            file_put_contents($path, $tmpFileHandle);

            $file->type = mime_content_type($path) ?: 'application/octet-stream';
            $file->path = self::WEB_PATH . $fileName;
            $file->update();
            $this->clearChunks($fileId);

            $this->logger->debug('Delete the uploading file from database', ['fileId' => $fileId]);
            $uploadingFile = UploadingFile::findByFile($fileId);
            $uploadingFile?->delete();
        } finally {
            @fclose($tmpFileHandle);
        }

        try {
            $this->logger->debug('Trigger conversion service to convert the file', ['fileId' => $fileId]);
            $this->conversionService->convertFile($file->id);
        } catch (Throwable $throwable) {
            $this->logger->warning('Failed to convert file after upload');
            $this->logger->warning($throwable);
        }

        return $file;
    }

    /**
     * Removes all chunks for the given file
     *
     * @param int $fileId
     * @throws Exception
     */
    public function clearChunks(int $fileId): void
    {
        $this->logger->debug('Clear all chunks for file', ['fileId' => $fileId]);
        $chunks = UploadingFileChunk::findByFile($fileId);
        foreach ($chunks as $chunk) {
            @unlink($chunk->chunkPath);
            $chunk->delete();
        }
    }
}
