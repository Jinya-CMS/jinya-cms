<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:45
 */

namespace DataBundle\Services\Pages;


use DataBundle\Entity\Page;
use DataBundle\Services\Base\BaseStaticContentService;
use Doctrine\ORM\EntityManager;
use HelperBundle\Services\Slug\SlugServiceInterface;

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