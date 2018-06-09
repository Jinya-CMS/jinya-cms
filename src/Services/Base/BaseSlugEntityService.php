<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Jinya\Entity\SlugEntity;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Services\Slug\SlugServiceInterface;

class BaseSlugEntityService extends BaseService
{
    /** @var SlugServiceInterface */
    protected $slugService;

    /**
     * BaseSlugEntityService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param string $entityType
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, string $entityType)
    {
        parent::__construct($entityManager, $entityType);
        $this->slugService = $slugService;
    }

    /**
     * @param SlugEntity $entity
     * @return \Jinya\Entity\BaseEntity|SlugEntity
     * @throws EmptySlugException
     */
    public function saveOrUpdate($entity)
    {
        if (null === $entity->getSlug() || '' === $entity->getSlug()) {
            if (method_exists($entity, 'getTitle')) {
                $slugBase = $entity->getTitle();
            } elseif (method_exists($entity, 'getName')) {
                $slugBase = $entity->getName();
            } else {
                throw new EmptySlugException();
            }

            $entity->setSlug($this->slugService->generateSlug($slugBase));
        }

        $entity->setSlug($entity->getSlug());

        return parent::saveOrUpdate($entity);
    }

    /**
     * {@inheritdoc}
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function get(string $slug)
    {
        $query = $this->getQueryBuilder()
            ->where('entity.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery();

        return $query->getSingleResult();
    }
}
