<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace Jinya\Services\Artworks;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Label\Label;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceInterface;
use Jinya\Services\Labels\LabelServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;

class ArtworkService implements ArtworkServiceInterface
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
     * GalleryService constructor.
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
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Artwork::class);
    }

    /**
     * Gets a list of artworks in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param Label|null $label
     * @return Artwork[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', Label $label = null): array
    {
        return $this->labelEntityService->getAll($this->entityManager->createQueryBuilder(), $offset, $count, $keyword, $label);
    }

    /**
     * Counts all artworks filtered by the given keyword
     *
     * @param string $keyword
     * @param Label|null $label
     * @return int
     */
    public function countAll(string $keyword = '', Label $label = null): int
    {
        return $this->labelEntityService->countAll($this->entityManager->createQueryBuilder(), $keyword, $label);
    }

    /**
     * Saves or update the given artwork
     *
     * @param Artwork $artwork
     * @return Artwork
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate($artwork): Artwork
    {
        $this->baseService->saveOrUpdate($artwork);

        return $artwork;
    }

    /**
     * Deletes the given gallery
     *
     * @param Artwork $artwork
     */
    public function delete(Artwork $artwork): void
    {
        $this->baseService->delete($artwork);
    }

    /**
     * Gets the artwork by slug or id
     *
     * @param string $slug
     * @return Artwork
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): ?Artwork
    {
        return $this->baseService->get($slug);
    }
}
