<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace Jinya\Services\Base;

use Doctrine\ORM\EntityManagerInterface;
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
     * @return SlugEntity
     * @throws EmptySlugException
     */
    public function saveOrUpdate($entity)
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

        $entity->setSlug($this->slugService->generateSlug($entity->getSlug()));

        return parent::saveOrUpdate($entity);
    }

    /**
     * @inheritdoc
     */
    public function get(string $slug)
    {
        return $this->getQueryBuilder()
            ->where('entity.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }
}