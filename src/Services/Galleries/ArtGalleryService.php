<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 08.11.2017
 * Time: 17:06
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Label\Label;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Galleries\ArtGalleryEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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
    public function __construct(
        EntityManagerInterface $entityManager,
        SlugServiceInterface $slugService,
        LabelEntityServiceInterface $labelEntityService,
        EventDispatcherInterface $eventDispatcher
    ) {
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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function get(string $slug): ArtGallery
    {
        $this->eventDispatcher->dispatch(new ArtGalleryEvent(null, $slug), ArtGalleryEvent::PRE_GET);

        $gallery = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(new ArtGalleryEvent($gallery, $slug), ArtGalleryEvent::POST_GET);

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
        $this->eventDispatcher->dispatch(
            new ListEvent($offset, $count, $keyword, []),
            ListEvent::ART_GALLERIES_PRE_GET_ALL
        );

        $galleries = $this->labelEntityService->getAll(
            $this->getBasicQueryBuilder(),
            $offset,
            $count,
            $keyword,
            $label
        );

        $this->eventDispatcher->dispatch(
            new ListEvent($offset, $count, $keyword, $galleries),
            ListEvent::ART_GALLERIES_POST_GET_ALL
        );

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
     * @param Label|null $label
     * @return int
     */
    public function countAll(string $keyword = '', Label $label = null): int
    {
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::ART_GALLERIES_PRE_COUNT);

        $count = $this->labelEntityService->countAll($this->getBasicQueryBuilder(), $keyword, $label);

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::ART_GALLERIES_POST_COUNT);

        return $count;
    }

    /**
     * Saves or updates the given gallery
     *
     * @param ArtGallery $gallery
     * @return ArtGallery
     * @throws EmptySlugException
     */
    public function saveOrUpdate(ArtGallery $gallery): ArtGallery
    {
        $pre = $this->eventDispatcher->dispatch(
            new ArtGalleryEvent($gallery, $gallery->getSlug()),
            ArtGalleryEvent::PRE_SAVE
        );

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($gallery);
            $this->eventDispatcher->dispatch(
                new ArtGalleryEvent($gallery, $gallery->getSlug()),
                ArtGalleryEvent::POST_SAVE
            );
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
        $pre = $this->eventDispatcher->dispatch(
            new ArtGalleryEvent($gallery, $gallery->getSlug()),
            ArtGalleryEvent::PRE_DELETE
        );

        if (!$pre->isCancel()) {
            $this->baseService->delete($gallery);
            $this->eventDispatcher->dispatch(
                new ArtGalleryEvent($gallery, $gallery->getSlug()),
                ArtGalleryEvent::POST_DELETE
            );
        }
    }
}
