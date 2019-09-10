<?php

namespace Jinya\Services\Media;

use Jinya\Entity\Media\Folder;

interface FolderServiceInterface
{
    /**
     * Gets a list of all folders in the folder and filtered by the given keyword and tag
     *
     * @param string $keyword
     * @param Folder|null $folder
     * @param string $tag
     * @return Folder[]
     */
    public function getAll(string $keyword = '', Folder $folder = null, string $tag = ''): array;

    /**
     * Counts all folders filtered by the given keyword in the given folder and tag
     *
     * @param string $keyword
     * @param Folder|null $folder
     * @param string $tag
     * @return int
     */
    public function countAll(string $keyword = '', Folder $folder = null, string $tag = ''): int;

    /**
     * Saves or update the given folder
     *
     * @param Folder $folder
     * @return Folder
     */
    public function saveOrUpdate(Folder $folder): Folder;

    /**
     * Deletes the given folder
     *
     * @param Folder $folder
     */
    public function delete(Folder $folder): void;

    /**
     * Gets the folder by slug or id
     *
     * @param int $id
     * @return Folder
     */
    public function get(int $id): Folder;
}
