<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Form;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Form\Form;
use Jinya\Exceptions\EmptySlugException;
use Jinya\Framework\Events\Common\CountEvent;
use Jinya\Framework\Events\Common\ListEvent;
use Jinya\Framework\Events\Form\FormEvent;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FormService implements FormServiceInterface
{
    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var SlugServiceInterface */
    private $slugService;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * FormService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService, EventDispatcherInterface $eventDispatcher)
    {
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Form::class);
        $this->entityManager = $entityManager;
        $this->slugService = $slugService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the specified @param string $slug
     * @return Form
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @see Form by slug
     */
    public function get(string $slug): Form
    {
        $this->eventDispatcher->dispatch(FormEvent::PRE_GET, new FormEvent(null, $slug));

        $form = $this->baseService->get($slug);

        $this->eventDispatcher->dispatch(FormEvent::POST_GET, new FormEvent($form, $slug));

        return $form;
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
        $this->eventDispatcher->dispatch(ListEvent::FORMS_PRE_GET_ALL, new ListEvent($offset, $count, $keyword, []));

        $items = $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->select('form')
            ->getQuery()
            ->getResult();

        $this->eventDispatcher->dispatch(ListEvent::FORMS_POST_GET_ALL, new ListEvent($offset, $count, $keyword, $items));

        return $items;
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
     * @throws NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        $this->eventDispatcher->dispatch(CountEvent::FORMS_PRE_COUNT, new CountEvent($keyword, -1));

        $count = $this->getFilteredQueryBuilder($keyword)
            ->select('COUNT(form)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->eventDispatcher->dispatch(CountEvent::FORMS_POST_COUNT, new CountEvent($keyword, $count));

        return $count;
    }

    /**
     * Saves or updates the given @param Form $form
     * @return Form
     * @throws EmptySlugException
     * @see Form
     */
    public function saveOrUpdate(Form $form): Form
    {
        $pre = $this->eventDispatcher->dispatch(FormEvent::PRE_SAVE, new FormEvent($form, $form->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->saveOrUpdate($form);
            $this->eventDispatcher->dispatch(FormEvent::POST_SAVE, new FormEvent($form, $form->getSlug()));
        }

        return $form;
    }

    /**
     * Deletes the given @see Form
     *
     * @param Form $form
     */
    public function delete(Form $form): void
    {
        $pre = $this->eventDispatcher->dispatch(FormEvent::PRE_DELETE, new FormEvent($form, $form->getSlug()));

        if (!$pre->isCancel()) {
            $this->baseService->delete($form);
            $this->eventDispatcher->dispatch(FormEvent::POST_DELETE, new FormEvent($form, $form->getSlug()));
        }
    }
}
