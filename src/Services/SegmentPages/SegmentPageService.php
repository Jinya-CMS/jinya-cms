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

    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

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
        $this->eventDispatcher->dispatch(new SegmentPageEvent(null, $slug), SegmentPageEvent::PRE_GET);
        $segmentPage = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(new SegmentPageEvent($segmentPage, $slug), SegmentPageEvent::POST_GET);

        return $segmentPage;
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param string $keyword
     * @return SegmentPage[]
     */
    public function getAll(string $keyword = ''): array
    {
        $this->eventDispatcher->dispatch(
            new ListEvent($keyword, []),
            ListEvent::SEGMENT_PAGE_PRE_GET_ALL
        );

        $items = $this->getFilteredQueryBuilder($keyword)
            ->select('segmentPage')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(
            new ListEvent($keyword, $items),
            ListEvent::SEGMENT_PAGE_POST_GET_ALL
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
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::SEGMENT_PAGES_PRE_COUNT);

        $count = $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(segmentPage)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::SEGMENT_PAGES_POST_COUNT);

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
            new SegmentPageEvent($segmentPage, $segmentPage->getSlug()),
            SegmentPageEvent::PRE_SAVE
        );

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($segmentPage);
            $this->eventDispatcher->dispatch(
                new SegmentPageEvent($segmentPage, $segmentPage->getSlug()),
                SegmentPageEvent::POST_SAVE
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
            new SegmentPageEvent($segmentPage, $segmentPage->getSlug()),
            SegmentPageEvent::PRE_DELETE
        );

        if (!$pre->isCancel()) {
            $this->baseService->delete($segmentPage);
            $this->eventDispatcher->dispatch(
                new SegmentPageEvent($segmentPage, $segmentPage->getSlug()),
                SegmentPageEvent::POST_DELETE
            );
        }
    }
}
