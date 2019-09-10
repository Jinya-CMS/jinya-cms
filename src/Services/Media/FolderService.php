<?php

namespace Jinya\Services\Media;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Media\Folder;
use Jinya\Framework\Events\Media\FolderEvent;
use Jinya\Services\Base\BaseService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FolderService implements FolderServiceInterface
{
    /** @var BaseService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * FolderService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->baseService = new BaseService($entityManager, Folder::class);
    }

    /**
     * Gets a list of all folders in the folder and filtered by the given keyword and tag
     *
     * @param string $keyword
     * @param Folder|null $folder
     * @param string $tag
     * @return Folder[]
     */
    public function getAll(string $keyword = '', Folder $folder = null, string $tag = ''): array
    {
        return $this
            ->getFilteredQueryBuilder($keyword, $folder, $tag)
            ->select('folder')
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
            ->from(Folder::class, 'folder');
        if ($keyword !== '') {
            $queryBuilder
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->like('folder.name', ':keyword')
                )
                ->setParameter(':keyword', $keyword);
        }

        if ($folder !== null) {
            $queryBuilder
                ->join('folder.folder', 'folder')
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->eq('folder.id', ':folderId')
                )
                ->setParameter(':folderId', $folder->getId());
        }

        if ($tag !== '') {
            $queryBuilder
                ->join('folder.tags', 'tags')
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
     * Counts all folders filtered by the given keyword in the given folder and tag
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
            ->select('COUNT(folder)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or update the given folder
     *
     * @param Folder $folder
     * @return Folder
     */
    public function saveOrUpdate(Folder $folder): Folder
    {
        $this->eventDispatcher->dispatch(new FolderEvent($folder, $folder->getId()), FolderEvent::PRE_SAVE);

        $returnValue = $this->baseService->saveOrUpdate($folder);

        $this->eventDispatcher->dispatch(new FolderEvent($folder, $folder->getId()), FolderEvent::POST_SAVE);

        return $returnValue;
    }

    /**
     * Deletes the given folder
     *
     * @param Folder $folder
     */
    public function delete(Folder $folder): void
    {
        $this->eventDispatcher->dispatch(new FolderEvent($folder, $folder->getId()), FolderEvent::PRE_DELETE);

        $this->baseService->delete($folder);

        $this->eventDispatcher->dispatch(new FolderEvent($folder, $folder->getId()), FolderEvent::POST_DELETE);
    }

    /**
     * Gets the folder by slug or id
     *
     * @param int $id
     * @return Folder
     */
    public function get(int $id): Folder
    {
        return $this->entityManager->find(Folder::class, $id);
    }
}
