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
use Jinya\Services\Base\BaseArtService;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;

class ArtworkService extends BaseArtService implements ArtworkServiceInterface
{
    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * ArtworkService constructor.
     * @param EntityManagerInterface $entityManager
     * @param MediaServiceInterface $mediaService
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     */
    public function __construct(EntityManagerInterface $entityManager, MediaServiceInterface $mediaService, SlugServiceInterface $slugService, LabelServiceInterface $labelService)
    {
        parent::__construct($entityManager, $slugService, $labelService, Artwork::class);
        $this->mediaService = $mediaService;
    }

    /**
     * @inheritdoc
     */
    public function get($idOrSlug): ?Artwork
    {
        return parent::get($idOrSlug);
    }

    /**
     * @inheritdoc
     */
    public function saveOrUpdate(Artwork $artwork): ?Artwork
    {
        return parent::save($artwork);
    }

    /**
     * @inheritdoc
     */
    public function getBySlug(string $slug): ?Artwork
    {
        return parent::getBySlug($slug);
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): ?Artwork
    {
        return parent::getById($id);
    }
}