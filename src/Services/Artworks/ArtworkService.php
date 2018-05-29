<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace Jinya\Services\Artworks;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Artwork;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceTrait;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;

class ArtworkService extends BaseSlugEntityService implements ArtworkServiceInterface
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
        parent::__construct($entityManager, $slugService, Artwork::class);
        $this->labelService = $labelService;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $slug): ?Artwork
    {
        return parent::get($slug);
    }
}
