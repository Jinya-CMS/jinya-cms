<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace Jinya\Services\SegmentPages;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\SegmentPage\SegmentPage;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\SegmentPages\SegmentPageEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SegmentPageService implements SegmentPageServiceInterface
{
    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * SegmentPageService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlugServiceInterface $slugService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, SegmentPage::class);
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the specified @param string $slug
     * @return SegmentPage
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @see SegmentPage by slug
     */
    public function get(string $slug): SegmentPage
    {
        $this->eventDispatcher->dispatch(SegmentPageEvent::PRE_GET, new SegmentPageEvent(null, $slug));
        $segmentPage = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(SegmentPageEvent::POST_GET, new SegmentPageEvent($segmentPage, $slug));

        return $segmentPage;
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return SegmentPage[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        $this->eventDispatcher->dispatch(
            ListEvent::SEGMENT_PAGE_PRE_GET_ALL,
            new ListEvent($offset, $count, $keyword, [])
        );

        $items = $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('segmentPage')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            ListEvent::SEGMENT_PAGE_POST_GET_ALL,
            new ListEvent($offset, $count, $keyword, $items)
        );

        return $items;
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @return QueryBuilder
     */
    private function getFilteredQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(SegmentPage::class, 'segmentPage')
            ->where('segmentPage.name LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(CountEvent::SEGMENT_PAGES_PRE_COUNT, new CountEvent($keyword, -1));

        $count = $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(segmentPage)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(CountEvent::SEGMENT_PAGES_POST_COUNT, new CountEvent($keyword, $count));

        return $count;
    }

    /**
     * Saves or updates the given @param SegmentPage $segmentPage
     * @return SegmentPage
     * @throws EmptySlugException
     * @see SegmentPage
     */
    public function saveOrUpdate(SegmentPage $segmentPage): SegmentPage
    {
        $pre = $this->eventDispatcher->dispatch(
            SegmentPageEvent::PRE_SAVE,
            new SegmentPageEvent($segmentPage, $segmentPage->getSlug())
        );

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($segmentPage);
            $this->eventDispatcher->dispatch(
                SegmentPageEvent::POST_SAVE,
                new SegmentPageEvent($segmentPage, $segmentPage->getSlug())
            );
        }

        return $segmentPage;
    }

    /**
     * Deletes the given @param SegmentPage $segmentPage
     * @see SegmentPage
     */
    public function delete(SegmentPage $segmentPage): void
    {
        $pre = $this->eventDispatcher->dispatch(
            SegmentPageEvent::PRE_DELETE,
            new SegmentPageEvent($segmentPage, $segmentPage->getSlug())
        );

        if (!$pre->isCancel()) {
            $this->baseService->delete($segmentPage);
            $this->eventDispatcher->dispatch(
                SegmentPageEvent::POST_DELETE,
                new SegmentPageEvent($segmentPage, $segmentPage->getSlug())
            );
        }
    }
}
