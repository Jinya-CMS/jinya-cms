<?php

namespace App\Storage;

use App\Database\Exceptions\EmptyResultException;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\UploadingFile;
use App\Database\UploadingFileChunk;
use RuntimeException;

class FileUploadService extends StorageBaseService
{
    /**
     * Saves a new file chunk and returns it
     *
     * @param string|resource $data
     * @throws EmptyResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function saveChunk(int $fileId, int $position, $data): UploadingFileChunk
    {
        $path = tempnam(sys_get_temp_dir(), 'upload-chunk');
        file_put_contents($path, $data);

        $uploadingFile = UploadingFile::findByFile($fileId);
        if (null === $uploadingFile) {
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
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function finishUpload(int $fileId): File
    {
        $file = File::findById($fileId);
        $chunks = UploadingFileChunk::findByFile($fileId);

        if (!@mkdir(self::SAVE_PATH, 0775, true) && !@is_dir(self::SAVE_PATH)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', self::SAVE_PATH));
        }

        $tmpFileHandle = tmpfile();

        try {
            foreach ($chunks as $chunk) {
                $chunkFileHandle = fopen($chunk->chunkPath, 'rb');

                try {
                    if (filesize($chunk->chunkPath) > 0) {
                        $chunkData = fread($chunkFileHandle, filesize($chunk->chunkPath));
                        fwrite($tmpFileHandle, $chunkData);
                    }
                } finally {
                    fclose($chunkFileHandle);
                }
            }

            $fileName = $this->getFileHash($tmpFileHandle);
            $path = self::SAVE_PATH . $fileName;

            rewind($tmpFileHandle);
            file_put_contents($path, $tmpFileHandle);

            $file->type = mime_content_type($path);
            $file->path = self::WEB_PATH . $fileName;
            $file->update();
            $this->clearChunks($fileId);

            $uploadingFile = UploadingFile::findByFile($fileId);
            /* @noinspection NullPointerExceptionInspection */
            $uploadingFile->delete();
        } finally {
            fclose($tmpFileHandle);
        }

        return $file;
    }

    /**
     * Removes all chunks for the given file
     *
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
