<?php

namespace Jinya\Services\Media;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Media\File;
use Jinya\Entity\Media\Folder;
use Jinya\Framework\Events\Media\FileEvent;
use Jinya\Services\Base\BaseService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FileService implements FileServiceInterface
{
    /** @var BaseService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * FileService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->baseService = new BaseService($entityManager, File::class);
    }

    /**
     * Gets a list of all files in the folder and filtered by the given keyword and tag
     *
     * @param string $keyword
     * @param Folder|null $folder
     * @param string $tag
     * @return File[]
     */
    public function getAll(string $keyword = '', Folder $folder = null, string $tag = ''): array
    {
        return $this
            ->getFilteredQueryBuilder($keyword, $folder, $tag)
            ->select('file')
            ->getQuery()
            ->getArrayResult();
    }

    private function getFilteredQueryBuilder(
        string $keyword = '',
        Folder $folder = null,
        string $tag = ''
    ): QueryBuilder {
        $queryBuilder = $this->entityManager
            ->createQueryBuilder()
            ->from(File::class, 'file');
        if ($keyword !== '') {
            $queryBuilder
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->like('file.name', ':keyword')
                )
                ->setParameter(':keyword', $keyword);
        }

        if ($folder !== null) {
            $queryBuilder
                ->join('file.folder', 'folder')
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->eq('folder.id', ':folderId')
                )
                ->setParameter(':folderId', $folder->getId());
        }

        if ($tag !== '') {
            $queryBuilder
                ->join('file.tags', 'tags')
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->like('tags.tag', ':tag')
                )
                ->setParameter(':tag', $tag);
        }

        return $queryBuilder;
    }

    /**
     * Counts all files filtered by the given keyword in the given folder and tag
     *
     * @param string $keyword
     * @param Folder|null $folder
     * @param string $tag
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = '', Folder $folder = null, string $tag = ''): int
    {
        return $this
            ->getFilteredQueryBuilder($keyword, $folder, $tag)
            ->select('COUNT(file)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or update the given file
     *
     * @param File $file
     * @return File
     */
    public function saveOrUpdate(File $file): File
    {
        $this->eventDispatcher->dispatch(new FileEvent($file, $file->getId()), FileEvent::PRE_SAVE);

        $returnValue = $this->baseService->saveOrUpdate($file);

        $this->eventDispatcher->dispatch(new FileEvent($file, $file->getId()), FileEvent::POST_SAVE);

        return $returnValue;
    }

    /**
     * Deletes the given file
     *
     * @param File $file
     */
    public function delete(File $file): void
    {
        $this->eventDispatcher->dispatch(new FileEvent($file, $file->getId()), FileEvent::PRE_DELETE);

        $this->baseService->delete($file);

        $this->eventDispatcher->dispatch(new FileEvent($file, $file->getId()), FileEvent::POST_DELETE);
    }

    /**
     * Gets the file by slug or id
     *
     * @param int $id
     * @return File
     */
    public function get(int $id): File
    {
        return $this->entityManager->find(File::class, $id);
    }
}
