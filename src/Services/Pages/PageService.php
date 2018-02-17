<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.12.2017
 * Time: 17:45
 */

namespace Jinya\Services\Pages;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Page;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\StaticContentServiceTrait;
use Jinya\Services\Slug\SlugServiceInterface;

class PageService extends BaseSlugEntityService implements PageServiceInterface
{
    use StaticContentServiceTrait;

    /**
     * PageService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService)
    {
        parent::__construct($entityManager, $slugService, Page::class);
    }
}