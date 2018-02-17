<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace Jinya\Services\Base;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\ArtEntityInterface;
use Jinya\Entity\Artwork;
use Jinya\Entity\Label;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;
use function method_exists;

abstract class BaseArtService extends BaseService implements BaseArtServiceInterface
{

    /** @var SlugServiceInterface */
    protected $slugService;
    /** @var LabelServiceInterface */
    protected $labelService;
    /** @var string */
    protected $entityType;

    /**
     * BaseArtworkService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     * @param string $entityType
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, LabelServiceInterface $labelService, string $entityType)
    {
        parent::__construct($entityManager, $entityType);
        $this->slugService = $slugService;
        $this->labelService = $labelService;
        $this->entityType = $entityType;
    }

    /**
     * @inheritdoc
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = '', Label $label = null): array
    {
        return $this->getFilteredQueryBuilder($keyword, $label)
            ->setFirstResult($offset)
            ->select($this->getFieldsWithoutClutter())
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a @see QueryBuilder with a keyword filter
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
    public function get($idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            return $this->getById($idOrSlug);
        }
        return $this->getBySlug($idOrSlug);
    }

    /**
     * @inheritdoc
     */
    public function getBySlug(string $slug)
    {
        return $this->getQueryBuilder()
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
        if ($entity->getSlug() === null) {
            $entity->setSlug($this->slugService->generateSlug($entity->getName()));
        }

        if (method_exists($entity, 'getLabelsChoice')) {
            /** @var Label[] $labels */
            $labels = $entity->getLabelsChoice();

            foreach ($labels as $label) {
                $label = $this->labelService->getLabel($label->getName());

                if ($entity instanceof Artwork) {
                    if (!$label->getArtworks()->contains($entity)) {
                        $label->getArtworks()->add($entity);
                    }
                } else {
                    if (!$label->getGalleries()->contains($entity)) {
                        $label->getGalleries()->add($entity);
                    }
                }

                if (!$entity->getLabels()->contains($label)) {
                    $entity->getLabels()->add($label);
                }
            }
        }

        return parent::save($entity);
    }
}