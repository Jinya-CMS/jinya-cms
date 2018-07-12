<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Form\Form;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;

class FormService implements FormServiceInterface
{
    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * FormService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService)
    {
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Form::class);
        $this->entityManager = $entityManager;
        $this->slugService = $slugService;
    }

    /**
     * Gets the specified @see Form by slug
     *
     * @param string $slug
     * @return Form
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): Form
    {
        return $this->baseService->get($slug);
    }

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Form[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        return $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('form')
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
            ->from(Form::class, 'form')
            ->where('form.title LIKE :keyword')
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
            ->select('COUNT(form)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or updates the given @see Form
     *
     * @param Form $form
     * @return Form
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate(Form $form): Form
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->baseService->saveOrUpdate($form);
    }

    /**
     * Deletes the given @see Form
     *
     * @param Form $form
     */
    public function delete(Form $form): void
    {
        $this->baseService->delete($form);
    }
}
