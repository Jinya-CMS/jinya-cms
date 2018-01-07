<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace DataBundle\Services\Base;


use DataBundle\Entity\ArtEntityInterface;
use DataBundle\Entity\Label;
use DataBundle\Services\Labels\LabelServiceInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use HelperBundle\Services\Slug\SlugServiceInterface;
use function method_exists;

abstract class BaseArtService extends BaseService implements BaseArtServiceInterface
{
    /** @var string */
    private $entityType;

    /** @var EntityRepository */
    private $repository;

    /** @var SlugServiceInterface */
    private $slugService;

    /** @var LabelServiceInterface */
    private $labelService;

    /**
     * BaseArtworkService constructor.
     * @param EntityManager $entityManager
     * @param SlugServiceInterface $slugService
     * @param string $entityType
     */
    public function __construct(EntityManager $entityManager, SlugServiceInterface $slugService, LabelServiceInterface $labelService, string $entityType)
    {
        parent::__construct($entityManager);
        $this->entityType = $entityType;
        $this->slugService = $slugService;
        $this->labelService = $labelService;
    }

    /**
     * @inheritdoc
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = '', Label $label = null): array
    {
        return $this->getFilteredQueryBuilder($keyword, $label)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @param Label|null $label
     * @return QueryBuilder
     */
    protected function getFilteredQueryBuilder(string $keyword, Label $label = null): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder = $queryBuilder
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('entity.description', ':keyword'),
                $queryBuilder->expr()->like('entity.name', ':keyword')
            ))
            ->setParameter('keyword', "%$keyword%");

        if ($label !== null) {
            $queryBuilder->andWhere(':label_id MEMBER OF entity.labels')->setParameter('label_id', $label->getId());
        }

        return $queryBuilder;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository($this->entityType);
        }

        return $this->repository->createQueryBuilder('entity');
    }

    /**
     * @inheritdoc
     */
    public function countAll(string $keyword = '', Label $label = null): int
    {
        $queryBuilder = $this->getFilteredQueryBuilder($keyword, $label);

        return $queryBuilder
            ->select($queryBuilder->expr()->count('entity'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        $this->getQueryBuilder()
            ->delete($this->entityType, 'entity')
            ->where('entity.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $idOrSlug int|string
     * @return mixed
     */
    public function get($idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            return $this->getById($idOrSlug);
        }
        return $this->getBySlug($idOrSlug);
    }

    /**
     * Gets the entity by id
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->entityManager->find($this->entityType, $id);
    }

    /**
     * Gets the entity by slug
     *
     * @param string $slug
     * @return mixed
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
     * @param ArtEntityInterface $entity
     * @return mixed
     */
    public function save($entity)
    {
        if ($entity->getSlug() === null) {
            $entity->setSlug($this->slugService->generateSlug($entity->getName()));
        }

        if (method_exists($entity, 'getLabels')) {
            $labels = $entity->getLabelsChoice();
            $this->labelService->createMissingLabels($labels);

            foreach ($labels as $label) {
                $entity->getLabels()->clear();
                $entity->getLabels()->add($this->labelService->getLabel($label->getName()));
            }
        }

        return parent::save($entity);
    }
}