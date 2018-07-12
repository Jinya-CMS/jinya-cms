<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Pages;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Page\Page;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;

class PageService implements PageServiceInterface
{
    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * PageService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService)
    {
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Page::class);
        $this->entityManager = $entityManager;
        $this->slugService = $slugService;
    }

    /**
     * Gets the specified @see Page by slug
     *
     * @param string $slug
     * @return Page
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): Page
    {
        return $this->baseService->get($slug);
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Page[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        return $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('page')
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @return QueryBuilder
     */
    private function getFilteredQueryBuilder(string $keyword)
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Page::class, 'page')
            ->where('page.title LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        return $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(page)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or updates the given @see Page
     *
     * @param Page $page
     * @return Page
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate(Page $page): Page
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->baseService->saveOrUpdate($page);
    }

    /**
     * Deletes the given @see Page
     *
     * @param Page $page
     */
    public function delete(Page $page): void
    {
        $this->baseService->delete($page);
    }

}
