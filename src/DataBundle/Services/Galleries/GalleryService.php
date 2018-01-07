<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace DataBundle\Services\Galleries;


use DataBundle\Entity\Gallery;
use DataBundle\Services\Base\BaseArtService;
use DataBundle\Services\Labels\LabelServiceInterface;
use Doctrine\ORM\EntityManager;
use HelperBundle\Services\Media\MediaServiceInterface;
use HelperBundle\Services\Slug\SlugServiceInterface;

class GalleryService extends BaseArtService implements GalleryServiceInterface
{

    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * GalleryService constructor.
     * @param EntityManager $entityManager
     * @param MediaServiceInterface $mediaService
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     */
    public function __construct(EntityManager $entityManager, MediaServiceInterface $mediaService, SlugServiceInterface $slugService, LabelServiceInterface $labelService)
    {
        parent::__construct($entityManager, $slugService, $labelService, Gallery::class);
        $this->mediaService = $mediaService;
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        $gallery = $this->get($id);
        if ($gallery->getBackground()) {
            $this->mediaService->deleteMedia($gallery->getBackground());
        }
        parent::delete($id);
    }

    /**
     * @inheritdoc
     */
    public function get($idOrSlug): ?Gallery
    {
        return parent::get($idOrSlug);
    }

    /**
     * @inheritdoc
     */
    public function getBySlug(string $slug): ?Gallery
    {
        return parent::getBySlug($slug);
    }


    /**
     * @inheritdoc
     */
    public function getById(int $id): ?Gallery
    {
        return parent::getById($id);
    }

    /**
     * @inheritdoc
     */
    public function saveOrUpdate(Gallery $gallery): Gallery
    {
        $background = $gallery->getBackgroundResource();
        if ($background !== null) {
            $gallery->setBackground($this->mediaService->saveMedia($background, MediaServiceInterface::GALLERY_BACKGROUND));
        }

        return parent::save($gallery);
    }

}