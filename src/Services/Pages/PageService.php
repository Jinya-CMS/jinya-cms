<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Pages;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Page\Page;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Pages\PageEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class PageService implements PageServiceInterface
{
    /** @var BaseSlugEntityService */
    private BaseSlugEntityService $baseService;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * PageService constructor.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        SlugServiceInterface $slugService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Page::class);
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the specified @param string $slug
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @see Page by slug
     */
    public function get(string $slug): Page
    {
        $this->eventDispatcher->dispatch(new PageEvent(null, $slug), PageEvent::PRE_GET);
        $page = $this->baseService->get($slug);
        $this->eventDispatcher->dispatch(new PageEvent($page, $slug), PageEvent::POST_GET);

        return $page;
    }

    /**
     * Gets all entities by the given parameters
     *
     * @return Page[]
     */
    public function getAll(string $keyword = ''): array
    {
        $this->eventDispatcher->dispatch(new ListEvent($keyword, []), ListEvent::PAGE_PRE_GET_ALL);

        $items = $this->getFilteredQueryBuilder($keyword)
            ->select('page')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(new ListEvent($keyword, $items), ListEvent::PAGE_POST_GET_ALL);

        return $items;
    }

    /**
     * Gets a querybuilder with a keyword filter
     */
    private function getFilteredQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Page::class, 'page')
            ->where('page.title LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all entities
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(new CountEvent($keyword, -1), CountEvent::PAGES_PRE_COUNT);

        $count = $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(page)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(new CountEvent($keyword, $count), CountEvent::PAGES_POST_COUNT);

        return $count;
    }

    /**
     * Saves or updates the given @param Page $page
     * @throws EmptySlugException
     * @see Page
     */
    public function saveOrUpdate(Page $page): Page
    {
        $pre = $this->eventDispatcher->dispatch(new PageEvent($page, $page->getSlug()), PageEvent::PRE_SAVE);

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($page);
            $this->eventDispatcher->dispatch(new PageEvent($page, $page->getSlug()), PageEvent::POST_SAVE);
        }

        return $page;
    }

    /**
     * Deletes the given @param Page $page
     * @see Page
     */
    public function delete(Page $page): void
    {
        $pre = $this->eventDispatcher->dispatch(new PageEvent($page, $page->getSlug()), PageEvent::PRE_DELETE);

        if (!$pre->isCancel()) {
            $this->baseService->delete($page);
            $this->eventDispatcher->dispatch(new PageEvent($page, $page->getSlug()), PageEvent::POST_DELETE);
        }
    }
}
