<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Galleries\ArtGallery;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceTrait;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;

class GalleryService extends BaseSlugEntityService implements GalleryServiceInterface
{
    use LabelEntityServiceTrait;

    /**
     * GalleryService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, LabelServiceInterface $labelService)
    {
        parent::__construct($entityManager, $slugService, ArtGallery::class);
        $this->labelService = $labelService;
    }
}
