<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 08:02
 */

namespace Jinya\Services\Form;


use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Form;
use Jinya\Entity\FormItem;

class FormItemService implements FormItemServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * FormItemService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Adds a new form item to the given form
     *
     * @param FormItem $formItem
     * @param Form $form
     */
    public function addItem(FormItem $formItem, Form $form): void
    {
        $position = $this->rearrangeFormItems($formItem->getPosition(), $form);
        $formItem->setPosition($position);

        $this->entityManager->persist($formItem);
        $this->entityManager->flush();
    }

    /**
     * @param int $position
     * @param Form $form
     * @return int
     */
    private function rearrangeFormItems(int $position, Form $form): int
    {
        $positions = $form->getItems()->toArray();
        uasort($positions, function ($a, $b) {
            /** @var FormItem $a */
            /** @var FormItem $b */
            return ($a->getPosition() > $b->getPosition()) ? -1 : 1;
        });

        $positions = array_values($positions);

        if ($position === -1) {
            $position = array_shift($positions)->getPosition() + 1;
        }

        /** @var FormItem $formItem */
        foreach ($positions as $key => $formItem) {
            $formItem->setPosition($key);
        }

        foreach ($positions as $formItem) {
            if ($formItem->getPosition() >= $position) {
                $formItem->setPosition($formItem->getPosition() + 1);
            }
        }

        $items = $form->getItems();

        /** @var ArrayIterator $iterator */
        $iterator = $items->getIterator();
        $iterator->uasort(function (FormItem $a, FormItem $b) {
            return ($a->getPosition() > $b->getPosition()) ? -1 : 1;
        });
        $form->setItems(new ArrayCollection(iterator_to_array($iterator)));

        return $position;
    }

    /**
     * Deletes the item at the given position
     *
     * @param Form $form
     * @param int $position
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

        $form->getItems()->removeElement($formItem);

        $this->entityManager->remove($formItem);
        $this->entityManager->flush();
    }

    /**
     * Gets all items for the given form
     *
     * @param string $formSlug
     * @return array
     */
    public function getItems(string $formSlug): array
    {
        return $this->entityManager->createQueryBuilder()
            ->select('item')
            ->from(FormItem::class, 'item')
            ->join('item.form', 'form')
            ->where('item.form = form')
            ->andWhere('form.slug = :slug')
            ->setParameter('slug', $formSlug)
            ->getQuery()
            ->getResult();
    }

    /**
     * Updates the given form item
     *
     * @param FormItem $formItem
     */
    public function updateItem(FormItem $formItem): void
    {
        $position = $this->rearrangeFormItems($formItem->getPosition(), $formItem->getForm());
        $formItem->setPosition($position);

        $this->entityManager->flush();
    }
}