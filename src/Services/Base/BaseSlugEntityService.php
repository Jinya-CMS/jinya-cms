<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace Jinya\Services\Base;

use Jinya\Entity\SlugEntity;
use Jinya\Services\Slug\SlugServiceInterface;

trait BaseSlugEntityService
{
    use BaseService {
        saveOrUpdate as protected baseSaveOrUpdate;
    }

    /** @var SlugServiceInterface */
    protected $slugService;

    /**
     * @inheritdoc
     */
    public function saveOrUpdate(SlugEntity $entity)
    {
        if (empty($entity->getSlug())) {
            $entity->setSlug($this->slugService->generateSlug($entity->getName()));
        }

        return $this->baseSaveOrUpdate($entity);
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