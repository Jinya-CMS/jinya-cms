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
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function array_values;

class FormItemService implements FormItemServiceInterface
{
    use ArrangementServiceTrait;

    private EntityManagerInterface $entityManager;

    private FormServiceInterface $formService;

    /** @noinspection PhpUndefinedClassInspection */
    private EventDispatcherInterface $eventDispatcher;

    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * FormItemService constructor.
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
     */
    public function addItem(FormItem $formItem): void
    {
        $pre = $this->eventDispatcher->dispatch(new FormItemEvent($formItem), FormItemEvent::PRE_ADD);
        if (!$pre->isCancel()) {
            $this->entityManager->persist($formItem);
            $this->rearrangeFormItems(-1, $formItem->getPosition(), $formItem, $formItem->getForm());
            $this->eventDispatcher->dispatch(new FormItemEvent($formItem), FormItemEvent::POST_ADD);
        }
    }

    private function rearrangeFormItems(int $oldPosition, int $newPosition, FormItem $formItem, Form $form): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new FormItemPositionEvent($form, $formItem, $oldPosition, $newPosition),
            FormItemPositionEvent::PRE_UPDATE
        );
        if (!$pre->isCancel()) {
            $positions = $form->getItems()->toArray();
            $positions = $this->rearrange($positions, $oldPosition, $newPosition, $formItem);

            $form->setItems(new ArrayCollection($positions));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                new FormItemPositionEvent($form, $formItem, $oldPosition, $newPosition),
                FormItemPositionEvent::POST_UPDATE
            );
        }
    }

    /**
     * Deletes the item at the given position
     *
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

        $pre = $this->eventDispatcher->dispatch(new FormItemEvent($formItem), FormItemEvent::PRE_DELETE);

        if (!$pre->isCancel()) {
            $this->entityManager->remove($formItem);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new FormItemEvent($formItem), FormItemEvent::POST_DELETE);
        }
    }

    /**
     * Gets all items for the given form
     */
    public function getItems(string $formSlug): array
    {
        $this->eventDispatcher->dispatch(
            new FormItemGetItemsEvent($formSlug, []),
            FormItemGetItemsEvent::PRE_GET_ITEMS
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
            new FormItemGetItemsEvent($formSlug, $items),
            FormItemGetItemsEvent::POST_GET_ITEMS
        );

        return $items;
    }

    /**
     * Updates the given form item
     */
    public function updateItem(FormItem $formItem): void
    {
        $this->eventDispatcher->dispatch(new FormItemEvent($formItem), FormItemEvent::PRE_UPDATE);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new FormItemEvent($formItem), FormItemEvent::POST_UPDATE);
    }

    /**
     * Gets the form item in the given form at the given position
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getItem(string $slug, int $position): FormItem
    {
        $this->eventDispatcher->dispatch(new FormItemGetItemEvent($slug, $position), FormItemGetItemEvent::PRE_GET);
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

        $this->eventDispatcher->dispatch(new FormItemEvent($item), FormItemEvent::POST_GET);

        return $item;
    }

    /**
     * Updates the position
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
