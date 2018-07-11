<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Label\Label;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;

class ArtGalleryService implements ArtGalleryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var SlugServiceInterface */
    private $slugService;

    /** @var LabelServiceInterface */
    private $labelService;

    /** @var LabelEntityServiceInterface */
    private $labelEntityService;

    /**
     * ArtGalleryService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param LabelServiceInterface $labelService
     * @param LabelEntityServiceInterface $labelEntityService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, LabelServiceInterface $labelService, LabelEntityServiceInterface $labelEntityService)
    {
        $this->labelService = $labelService;
        $this->labelEntityService = $labelEntityService;
        $this->entityManager = $entityManager;
        $this->slugService = $slugService;
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, ArtGallery::class);
    }

    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return ArtGallery
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): ArtGallery
    {
        return $this->baseService->get($slug);
    }

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return ArtGallery[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array
    {
        return $this->labelEntityService->getAll($this->getBasicQueryBuilder(), $offset, $count, $keyword, $label);
    }


    /**
     * @return QueryBuilder
     */
    private function getBasicQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('entity')
            ->from(ArtGallery::class, 'entity');
    }

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @param \Jinya\Entity\Label\Label|null $label
     * @return int
     */
    public function countAll(string $keyword = '', Label $label = null): int
    {
        return $this->labelEntityService->countAll($this->getBasicQueryBuilder(), $keyword, $label);
    }

    /**
     * Saves or updates the given gallery
     *
     * @param ArtGallery $gallery
     * @return ArtGallery
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate(ArtGallery $gallery): ArtGallery
    {
        $this->baseService->saveOrUpdate($gallery);

        return $gallery;
    }

    /**
     * Deletes the given gallery
     *
     * @param ArtGallery $gallery
     */
    public function delete(ArtGallery $gallery): void
    {
        $this->baseService->delete($gallery);
    }
}
