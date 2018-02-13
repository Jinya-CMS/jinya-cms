<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace ServiceBundle\Services\Base;


use DataBundle\Exceptions\EmptySlugException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use ServiceBundle\Services\Slug\SlugServiceInterface;
use function method_exists;

abstract class BaseStaticContentService extends BaseService implements BaseStaticContentServiceInterface
{
    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * BaseArtworkService constructor.
     * @param EntityManager $entityManager
     * @param SlugServiceInterface $slugService
     * @param string $entityType
     */
    public function __construct(EntityManager $entityManager, SlugServiceInterface $slugService, string $entityType)
    {
        parent::__construct($entityManager, $entityType);
        $this->slugService = $slugService;
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
    protected function getFilteredQueryBuilder(string $keyword)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('entity.title', ':keyword')
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
            ->select($queryBuilder->expr()->count('entity'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $idOrSlug int|string
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function get($idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            return $this->getById($idOrSlug);
        }
        return $this->getBySlug($idOrSlug);
    }

    /**
     * Gets the entity by slug
     *
     * @param string $slug
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBySlug(string $slug)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where('entity.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @inheritdoc
     */
    public function save($entity)
    {
        if ($entity->getSlug() === null || $entity->getSlug() === '') {
            if (method_exists($entity, 'getTitle')) {
                $slugBase = $entity->getTitle();
            } else if (method_exists($entity, 'getName')) {
                $slugBase = $entity->getName();
            } else {
                throw new EmptySlugException();
            }

            $entity->setSlug($this->slugService->generateSlug($slugBase));
        }

        return parent::save($entity);
    }
}