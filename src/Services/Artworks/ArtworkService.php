<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace Jinya\Services\Artworks;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Label\Label;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Artworks\ArtworkEvent;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Base\LabelEntityServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ArtworkService implements ArtworkServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var LabelEntityServiceInterface */
    private $labelEntityService;
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * ArtworkService constructor.
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
        $this->eventDispatcher->dispatch(new ListEvent($offset, $count, $keyword, []), ListEvent::ARTWORKS_PRE_GET_ALL);

        $artworks = $this->labelEntityService->getAll($this->getBasicQueryBuilder(), $offset, $count, $keyword, $label);

        $this->eventDispatcher->dispatch(
            new ListEvent($offset, $count, $keyword, $artworks),
            ListEvent::ARTWORKS_POST_GET_ALL
        );

        return $artworks;
    }

    /**
     * @return QueryBuilder
     */
    private function getBasicQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('entity')
            ->from(Artwork::class, 'entity');
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
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::ARTWORKS_PRE_COUNT);

        $count = $this->labelEntityService->countAll($this->getBasicQueryBuilder(), $keyword, $label);

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::ARTWORKS_POST_COUNT);

        return $count;
    }

    /**
     * Saves or update the given artwork
     *
     * @param Artwork $artwork
     * @return Artwork
     * @throws EmptySlugException
     */
    public function saveOrUpdate(Artwork $artwork): Artwork
    {
        $pre = $this->eventDispatcher->dispatch(
            new ArtworkEvent($artwork, $artwork->getSlug()),
            ArtworkEvent::PRE_SAVE
        );

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($artwork);
            $this->eventDispatcher->dispatch(new ArtworkEvent($artwork, $artwork->getSlug()), ArtworkEvent::POST_SAVE);
        }

        return $artwork;
    }

    /**
     * Deletes the given gallery
     *
     * @param Artwork $artwork
     */
    public function delete(Artwork $artwork): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new ArtworkEvent($artwork, $artwork->getSlug()),
            ArtworkEvent::PRE_DELETE
        );

        if (!$pre->isCancel()) {
            $this->baseService->delete($artwork);
            $this->eventDispatcher->dispatch(
                new ArtworkEvent($artwork, $artwork->getSlug()),
                ArtworkEvent::POST_DELETE
            );
        }
    }

    /**
     * Gets the artwork by slug or id
     *
     * @param string $slug
     * @return Artwork
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function get(string $slug): Artwork
    {
        $this->eventDispatcher->dispatch(new ArtworkEvent(null, $slug), ArtworkEvent::PRE_GET);

        $artwork = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(new ArtworkEvent($artwork, $slug), ArtworkEvent::POST_GET);

        return $artwork;
    }
}
