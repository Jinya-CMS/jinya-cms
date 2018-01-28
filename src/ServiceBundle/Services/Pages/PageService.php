<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:45
 */

namespace ServiceBundle\Services\Pages;


use DataBundle\Entity\Page;
use ServiceBundle\Services\Base\BaseStaticContentService;
use Doctrine\ORM\EntityManager;
use ServiceBundle\Services\Slug\SlugServiceInterface;

class PageService extends BaseStaticContentService implements PageServiceInterface
{
    /**
     * PageService constructor.
     * @param EntityManager $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManager $entityManager, SlugServiceInterface $slugService)
    {
        parent::__construct($entityManager, $slugService, Page::class);
    }
}