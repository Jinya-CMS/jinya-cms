<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 08:02
 */

namespace Jinya\Services\Form;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;
use Jinya\Framework\Events\Form\FormItemEvent;
use Jinya\Framework\Events\Form\FormItemGetItemEvent;
use Jinya\Framework\Events\Form\FormItemGetItemsEvent;
use Jinya\Framework\Events\Form\FormItemPositionEvent;
use Jinya\Services\Base\ArrangementServiceTrait;
use /** @noinspection PhpUndefinedClassInspection */
    Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function array_values;

class FormItemService implements FormItemServiceInterface
{
    use ArrangementServiceTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FormServiceInterface */
    private $formService;
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * FormItemService constructor.
     * @param EntityManagerInterface $entityManager
     * @param FormServiceInterface $formService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        FormServiceInterface $formService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->formService = $formService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Adds a new form item to the given form
     *
     * @param FormItem $formItem
     */
    public function addItem(FormItem $formItem): void
    {
        $pre = $this->eventDispatcher->dispatch(FormItemEvent::PRE_ADD, new FormItemEvent($formItem));
        if (!$pre->isCancel()) {
            $this->entityManager->persist($formItem);
            $this->rearrangeFormItems(-1, $formItem->getPosition(), $formItem, $formItem->getForm());
            $this->eventDispatcher->dispatch(FormItemEvent::POST_ADD, new FormItemEvent($formItem));
        }
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param FormItem $formItem
     * @param Form $form
     */
    private function rearrangeFormItems(int $oldPosition, int $newPosition, FormItem $formItem, Form $form): void
    {
        $pre = $this->eventDispatcher->dispatch(
            FormItemPositionEvent::PRE_UPDATE,
            new FormItemPositionEvent($form, $formItem, $oldPosition, $newPosition)
        );
        if (!$pre->isCancel()) {
            $positions = $form->getItems()->toArray();
            $positions = $this->rearrange($positions, $oldPosition, $newPosition, $formItem);

            $form->setItems(new ArrayCollection($positions));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                FormItemPositionEvent::POST_UPDATE,
                new FormItemPositionEvent($form, $formItem, $oldPosition, $newPosition)
            );
        }
    }

    /**
     * Deletes the item at the given position
     *
     * @param Form $form
     * @param int $position
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function deleteItem(Form $form, int $position): void
    {
        $formItem = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(FormItem::class, 'item')
            ->where('item.position = :position')
            ->andWhere('item.form = :form')
            ->setParameter('position', $position)
            ->setParameter('form', $form)
            ->getQuery()
            ->getSingleResult();

        $pre = $this->eventDispatcher->dispatch(FormItemEvent::PRE_DELETE, new FormItemEvent($formItem));

        if (!$pre->isCancel()) {
            $this->entityManager->remove($formItem);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(FormItemEvent::POST_DELETE, new FormItemEvent($formItem));
        }
    }

    /**
     * Gets all items for the given form
     *
     * @param string $formSlug
     * @return array
     */
    public function getItems(string $formSlug): array
    {
        $this->eventDispatcher->dispatch(
            FormItemGetItemsEvent::PRE_GET_ITEMS,
            new FormItemGetItemsEvent($formSlug, [])
        );
        $items = array_values($this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(FormItem::class, 'item')
            ->join('item.form', 'form')
            ->where('item.form = form')
            ->andWhere('form.slug = :slug')
            ->setParameter('slug', $formSlug)
            ->getQuery()
            ->getResult());

        $this->eventDispatcher->dispatch(
            FormItemGetItemsEvent::POST_GET_ITEMS,
            new FormItemGetItemsEvent($formSlug, $items)
        );

        return $items;
    }

    /**
     * Updates the given form item
     *
     * @param FormItem $formItem
     */
    public function updateItem(FormItem $formItem): void
    {
        $this->eventDispatcher->dispatch(FormItemEvent::PRE_UPDATE, new FormItemEvent($formItem));

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(FormItemEvent::POST_UPDATE, new FormItemEvent($formItem));
    }

    /**
     * Gets the form item in the given form at the given position
     *
     * @param string $slug
     * @param int $position
     * @return FormItem
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getItem(string $slug, int $position): FormItem
    {
        $this->eventDispatcher->dispatch(FormItemGetItemEvent::PRE_GET, new FormItemGetItemEvent($slug, $position));
        $item = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(FormItem::class, 'item')
            ->where('form.slug = :slug')
            ->andWhere('item.position = :position')
            ->join('item.form', 'form')
            ->setParameter('slug', $slug)
            ->setParameter('position', $position)
            ->getQuery()
            ->getSingleResult();

        $this->eventDispatcher->dispatch(FormItemEvent::POST_GET, new FormItemEvent($item));

        return $item;
    }

    /**
     * Updates the position
     *
     * @param string $formSlug
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function updatePosition(string $formSlug, int $oldPosition, int $newPosition): void
    {
        $form = $this->formService->get($formSlug);
        $items = $form->getItems();

        $formItem = $items->filter(static function (FormItem $item) use ($oldPosition) {
            return $item->getPosition() === $oldPosition;
        })->first();

        $this->rearrangeFormItems($oldPosition, $newPosition, $formItem, $form);
    }
}
