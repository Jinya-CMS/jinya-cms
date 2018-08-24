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
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Galleries\ArtGalleryEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArtGalleryService implements ArtGalleryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var LabelEntityServiceInterface */
    private $labelEntityService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * ArtGalleryService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param LabelEntityServiceInterface $labelEntityService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, LabelEntityServiceInterface $labelEntityService, EventDispatcherInterface $eventDispatcher)
    {
        $this->labelEntityService = $labelEntityService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->eventDispatcher->dispatch(ArtGalleryEvent::PRE_GET, new ArtGalleryEvent(null, $slug));

        $gallery = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(ArtGalleryEvent::POST_GET, new ArtGalleryEvent($gallery, $slug));

        return $gallery;
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
        $this->eventDispatcher->dispatch(ListEvent::ART_GALLERIES_PRE_GET_ALL, new ListEvent($offset, $count, $keyword, []));

        $galleries = $this->labelEntityService->getAll($this->getBasicQueryBuilder(), $offset, $count, $keyword, $label);

        $this->eventDispatcher->dispatch(ListEvent::ART_GALLERIES_POST_GET_ALL, new ListEvent($offset, $count, $keyword, $galleries));

        return $galleries;
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
        $this->eventDispatcher->dispatch(CountEvent::ART_GALLERIES_PRE_COUNT, new CountEvent($keyword, -1));

        $count = $this->labelEntityService->countAll($this->getBasicQueryBuilder(), $keyword, $label);

        $this->eventDispatcher->dispatch(CountEvent::ART_GALLERIES_POST_COUNT, new CountEvent($keyword, $count));

        return $count;
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
        $pre = $this->eventDispatcher->dispatch(ArtGalleryEvent::PRE_SAVE, new ArtGalleryEvent($gallery, $gallery->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($gallery);
            $this->eventDispatcher->dispatch(ArtGalleryEvent::POST_SAVE, new ArtGalleryEvent($gallery, $gallery->getSlug()));
        }

        return $gallery;
    }

    /**
     * Deletes the given gallery
     *
     * @param ArtGallery $gallery
     */
    public function delete(ArtGallery $gallery): void
    {
        $pre = $this->eventDispatcher->dispatch(ArtGalleryEvent::PRE_DELETE, new ArtGalleryEvent($gallery, $gallery->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->delete($gallery);
            $this->eventDispatcher->dispatch(ArtGalleryEvent::POST_DELETE, new ArtGalleryEvent($gallery, $gallery->getSlug()));
        }
    }
}
