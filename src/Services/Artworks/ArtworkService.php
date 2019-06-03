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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArtworkService implements ArtworkServiceInterface
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
        $this->eventDispatcher->dispatch(ListEvent::ARTWORKS_PRE_GET_ALL, new ListEvent($offset, $count, $keyword, []));

        $artworks = $this->labelEntityService->getAll($this->getBasicQueryBuilder(), $offset, $count, $keyword, $label);

        $this->eventDispatcher->dispatch(
            ListEvent::ARTWORKS_POST_GET_ALL,
            new ListEvent($offset, $count, $keyword, $artworks)
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
        $this->eventDispatcher->dispatch(CountEvent::ARTWORKS_PRE_COUNT, new CountEvent($keyword, -1));

        $count = $this->labelEntityService->countAll($this->getBasicQueryBuilder(), $keyword, $label);

        $this->eventDispatcher->dispatch(CountEvent::ARTWORKS_POST_COUNT, new CountEvent($keyword, $count));

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
            ArtworkEvent::PRE_SAVE,
            new ArtworkEvent($artwork, $artwork->getSlug())
        );

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($artwork);
            $this->eventDispatcher->dispatch(ArtworkEvent::POST_SAVE, new ArtworkEvent($artwork, $artwork->getSlug()));
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
            ArtworkEvent::PRE_DELETE,
            new ArtworkEvent($artwork, $artwork->getSlug())
        );

        if (!$pre->isCancel()) {
            $this->baseService->delete($artwork);
            $this->eventDispatcher->dispatch(
                ArtworkEvent::POST_DELETE,
                new ArtworkEvent($artwork, $artwork->getSlug())
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
    public function get(string $slug): ?Artwork
    {
        $this->eventDispatcher->dispatch(ArtworkEvent::PRE_GET, new ArtworkEvent(null, $slug));

        $artwork = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(ArtworkEvent::POST_GET, new ArtworkEvent($artwork, $slug));

        return $artwork;
    }
}
