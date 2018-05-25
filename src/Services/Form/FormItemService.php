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
use Jinya\Entity\Form;
use Jinya\Entity\FormItem;
use Jinya\Services\Base\ArrangementServiceTrait;
use function array_values;

class FormItemService implements FormItemServiceInterface
{
    use ArrangementServiceTrait;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var FormServiceInterface */
    private $formService;

    /**
     * FormItemService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormServiceInterface $formService
     */
    public function __construct(EntityManagerInterface $entityManager, FormServiceInterface $formService)
    {
        $this->entityManager = $entityManager;
        $this->formService = $formService;
    }

    /**
     * Adds a new form item to the given form
     *
     * @param FormItem $formItem
     */
    public function addItem(FormItem $formItem): void
    {
        $this->entityManager->persist($formItem);
        $this->rearrangeFormItems(-1, $formItem->getPosition(), $formItem, $formItem->getForm());
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param FormItem $formItem
     * @param Form $form
     */
    private function rearrangeFormItems(int $oldPosition, int $newPosition, FormItem $formItem, Form $form)
    {
        //FIXME Checkout artwork position service, there it works
        $positions = $form->getItems()->toArray();
        $positions = $this->rearrange($positions, $oldPosition, $newPosition, $formItem);

        $form->setItems(new ArrayCollection($positions));
        $this->entityManager->flush();
    }

    /**
     * Deletes the item at the given position
     *
     * @param Form $form
     * @param int $position
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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

        $this->entityManager->remove($formItem);
        $this->entityManager->flush();
    }

    /**
     * Gets all items for the given form
     *
     * @param string $formSlug
     *
     * @return array
     */
    public function getItems(string $formSlug): array
    {
        $items = $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(FormItem::class, 'item')
            ->join('item.form', 'form')
            ->where('item.form = form')
            ->andWhere('form.slug = :slug')
            ->setParameter('slug', $formSlug)
            ->getQuery()
            ->getResult();

        uasort($items, function (FormItem $a, FormItem $b) {
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
        });

        return array_values($items);
    }

    /**
     * Updates the given form item
     *
     * @param FormItem $formItem
     */
    public function updateItem(FormItem $formItem): void
    {
        $this->entityManager->flush();
    }

    /**
     * Gets the form item in the given form at the given position
     *
     * @param string $slug
     * @param int $position
     *
     * @return FormItem
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getItem(string $slug, int $position): FormItem
    {
        return $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(FormItem::class, 'item')
            ->where('form.slug = :slug')
            ->andWhere('item.position = :position')
            ->join('item.form', 'form')
            ->setParameter('slug', $slug)
            ->setParameter('position', $position)
            ->getQuery()
            ->getSingleResult();
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

        $artwork = $items->filter(function (FormItem $item) use ($oldPosition) {
            return $item->getPosition() === $oldPosition;
        })->first();

        $this->rearrangeFormItems($oldPosition, $newPosition, $artwork, $form);
    }
}
