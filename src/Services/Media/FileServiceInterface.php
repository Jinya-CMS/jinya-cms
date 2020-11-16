<?php

namespace Jinya\Services\Media;

use Jinya\Entity\Media\File;

interface FileServiceInterface
{
    /**
     * Gets a list of all files in the folder and filtered by the given keyword and tag
     *
     * @return File[]
     */
    public function getAll(string $keyword = '', string $tag = '', string $type = ''): array;

    /**
     * Counts all files filtered by the given keyword in the given folder and tag
     */
    public function countAll(string $keyword = '', string $tag = '', string $type = ''): int;

    /**
     * Saves or update the given file
     */
    public function saveOrUpdate(File $file): File;

    /**
     * Deletes the given file
     */
    public function delete(File $file): void;

    /**
     * Gets the file by id
     */
    public function get(int $id): File;

    /**
     * Gets the file by name
     */
    public function getByName(string $name): ?File;
}
