<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace DataBundle\Services\Galleries;


use DataBundle\Entity\Gallery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class GalleryService extends BaseService implements GalleryServiceInterface
{

    /** @var EntityRepository */
    private $repository;

    /**
     * GalleriesService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function get($idOrSlug): ?Gallery
    {
        if (is_numeric($idOrSlug)) {
            return $this->getById($idOrSlug);
        }
        return $this->getBySlug($idOrSlug);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): ?Gallery
    {
        return $this->entityManager->find(Gallery::class, $id);
    }

    /**
     * @inheritdoc
     */
    public function getBySlug(string $slug): ?Gallery
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where('g.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    private function getQueryBuilder(): QueryBuilder
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository(Gallery::class);
        }

        return $this->repository->createQueryBuilder('g');
    }

    /**
     * @inheritdoc
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = ''): array
    {
        return $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @return QueryBuilder
     */
    private function getFilteredQueryBuilder(string $keyword)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('g.description', ':keyword'),
                $queryBuilder->expr()->like('g.name', ':keyword')
            ))
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * @inheritdoc
     */
    public function countAll(string $keyword = ''): int
    {
        $queryBuilder = $this->getFilteredQueryBuilder($keyword);

        return $queryBuilder
            ->select($queryBuilder->expr()->count('g'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function saveOrUpdate(Gallery $gallery): Gallery
    {
        return parent::save($gallery);
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        $this->getQueryBuilder()
            ->delete('DataBundle:Gallery')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}