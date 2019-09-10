<?php


namespace Jinya\Services\Media;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Media\Gallery;
use Jinya\Framework\Events\Media\GalleryEvent;
use Jinya\Services\Base\BaseService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GalleryService
{
    /** @var BaseService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * GalleryService constructor.
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->baseService = new BaseService($entityManager, Gallery::class);
    }

    /**
     * Gets a list of all gallerys in the gallery and filtered by the given keyword and tag
     *
     * @param string $keyword
     * @param string $tag
     * @return Gallery[]
     */
    public function getAll(string $keyword = '', string $tag = ''): array
    {
        return $this
            ->getFilteredQueryBuilder($keyword, $tag)
            ->select('gallery')
            ->getQuery()
            ->getArrayResult();
    }

    private function getFilteredQueryBuilder(string $keyword = '', string $tag = ''): QueryBuilder
    {
        $queryBuilder = $this->entityManager
            ->createQueryBuilder()
            ->from(Gallery::class, 'gallery');
        if ($keyword !== '') {
            $queryBuilder
                ->andWhere(
                    $queryBuilder
                        ->expr()
                        ->like('gallery.name', ':keyword')
                )
                ->setParameter(':keyword', $keyword);
        }


        if ($tag !== '') {
            $queryBuilder
                ->join('gallery.tags', 'tags')
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
     * Counts all gallerys filtered by the given keyword in the given gallery and tag
     *
     * @param string $keyword
     * @param string $tag
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = '', string $tag = ''): int
    {
        return $this
            ->getFilteredQueryBuilder($keyword, $tag)
            ->select('COUNT(gallery)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or update the given gallery
     *
     * @param Gallery $gallery
     * @return Gallery
     */
    public function saveOrUpdate(Gallery $gallery): Gallery
    {
        $this->eventDispatcher->dispatch(new GalleryEvent($gallery, $gallery->getId()), GalleryEvent::PRE_SAVE);

        $returnValue = $this->baseService->saveOrUpdate($gallery);

        $this->eventDispatcher->dispatch(new GalleryEvent($gallery, $gallery->getId()), GalleryEvent::POST_SAVE);

        return $returnValue;
    }

    /**
     * Deletes the given gallery
     *
     * @param Gallery $gallery
     */
    public function delete(Gallery $gallery): void
    {
        $this->eventDispatcher->dispatch(new GalleryEvent($gallery, $gallery->getId()), GalleryEvent::PRE_DELETE);

        $this->baseService->delete($gallery);

        $this->eventDispatcher->dispatch(new GalleryEvent($gallery, $gallery->getId()), GalleryEvent::POST_DELETE);
    }

    /**
     * Gets the gallery by slug or id
     *
     * @param int $id
     * @return Gallery
     */
    public function get(int $id): Gallery
    {
        return $this->entityManager->find(Gallery::class, $id);
    }
}
