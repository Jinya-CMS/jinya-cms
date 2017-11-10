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
use HelperBundle\Services\Media\MediaServiceInterface;
use HelperBundle\Services\Slug\SlugServiceInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class GalleryService extends BaseService implements GalleryServiceInterface
{

    /** @var EntityRepository */
    private $repository;
    /** @var MediaServiceInterface */
    private $mediaService;
    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * GalleryService constructor.
     * @param EntityManager $entityManager
     * @param MediaServiceInterface $mediaService
     * @param SlugServiceInterface $slugService
     * @param TokenStorage $tokenStorage
     */
    public function __construct(EntityManager $entityManager, MediaServiceInterface $mediaService, SlugServiceInterface $slugService, TokenStorage $tokenStorage)
    {
        parent::__construct($entityManager, $tokenStorage);
        $this->mediaService = $mediaService;
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
        $background = $gallery->getBackgroundResource();
        if ($background !== null) {
            $gallery->setBackground($this->mediaService->saveMedia($background, MediaServiceInterface::GALLERY_BACKGROUND));
        }
        if ($gallery->getSlug() === null) {
            $gallery->setSlug($this->slugService->generateSlug($gallery->getName()));
        }

        return parent::save($gallery);
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        $gallery = $this->get($id);
        if ($gallery->getBackground()) {
            $this->mediaService->deleteMedia($gallery->getBackground());
        }

        $this->getQueryBuilder()
            ->delete('DataBundle:Gallery', 'g')
            ->where('g.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
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
}