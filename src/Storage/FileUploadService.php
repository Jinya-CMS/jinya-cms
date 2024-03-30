<?php

namespace Jinya\Cms\Storage;

use Jinya\Cms\Database\Exceptions\EmptyResultException;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\UploadingFile;
use Jinya\Cms\Database\UploadingFileChunk;
use Jinya\Cms\Utils\UuidGenerator;
use Exception;
use Jinya\Database\Exception\NotNullViolationException;
use RuntimeException;

/**
 * A simple helper to handle file uploads
 */
class FileUploadService extends StorageBaseService
{
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
        if ($data === null) {
            throw new RuntimeException();
        }

        $path = __JINYA_TEMP . UuidGenerator::generateV4();
        file_put_contents($path, $data);

        $uploadingFile = UploadingFile::findByFile($fileId);
        if ($uploadingFile === null) {
            throw new EmptyResultException('File not found');
        }

        $chunk = new UploadingFileChunk();
        $chunk->chunkPath = $path;
        $chunk->chunkPosition = $position;
        $chunk->uploadingFileId = $uploadingFile->id;
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
        $file = File::findById($fileId);
        if ($file === null) {
            throw new EmptyResultException('File not found');
        }

        $chunks = UploadingFileChunk::findByFile($fileId);

        if (!@mkdir(self::SAVE_PATH, 0775, true) && !@is_dir(self::SAVE_PATH)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', self::SAVE_PATH));
        }

        $tmpFileHandle = tmpfile();
        if (!is_resource($tmpFileHandle)) {
            return null;
        }

        try {
            foreach ($chunks as $chunk) {
                $chunkFileHandle = fopen($chunk->chunkPath, 'rb');

                if (is_resource($chunkFileHandle)) {
                    try {
                        if (filesize($chunk->chunkPath) > 0) {
                            $chunkData = fread($chunkFileHandle, filesize($chunk->chunkPath));
                            if (is_string($chunkData)) {
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

            rewind($tmpFileHandle);
            file_put_contents($path, $tmpFileHandle);

            $file->type = mime_content_type($path) ?: 'application/octet-stream';
            $file->path = self::WEB_PATH . $fileName;
            $file->update();
            $this->clearChunks($fileId);

            $uploadingFile = UploadingFile::findByFile($fileId);
            $uploadingFile?->delete();
        } finally {
            @fclose($tmpFileHandle);
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
        $chunks = UploadingFileChunk::findByFile($fileId);
        foreach ($chunks as $chunk) {
            @unlink($chunk->chunkPath);
            $chunk->delete();
        }
    }
}
