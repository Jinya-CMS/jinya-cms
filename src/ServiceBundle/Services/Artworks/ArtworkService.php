<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace ServiceBundle\Services\Artworks;

use DataBundle\Entity\Artwork;
use ServiceBundle\Services\Base\BaseArtService;
use ServiceBundle\Services\Labels\LabelServiceInterface;
use Doctrine\ORM\EntityManager;
use ServiceBundle\Services\Media\MediaServiceInterface;
use ServiceBundle\Services\Slug\SlugServiceInterface;

class ArtworkService extends BaseArtService implements ArtworkServiceInterface
{
    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * ArtworkService constructor.
     * @param EntityManager $entityManager
     * @param MediaServiceInterface $mediaService
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     */
    public function __construct(EntityManager $entityManager, MediaServiceInterface $mediaService, SlugServiceInterface $slugService, LabelServiceInterface $labelService)
    {
        parent::__construct($entityManager, $slugService, $labelService, Artwork::class);
        $this->mediaService = $mediaService;
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id): void
    {
        $artwork = $this->get($id);
        if ($artwork->getPicture()) {
            $this->mediaService->deleteMedia($artwork->getPicture());
        }

        parent::delete($id);
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
        $background = $artwork->getPictureResource();
        if ($background !== null) {
            $artwork->setPicture($this->mediaService->saveMedia($background, MediaServiceInterface::CONTENT_IMAGE));
        }

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