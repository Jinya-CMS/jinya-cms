<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 08:02
 */

namespace Jinya\Services\Form;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Form;
use Jinya\Entity\FormItem;
use function array_values;

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
     */
    public function addItem(FormItem $formItem): void
    {
        $position = $this->rearrangeFormItems($formItem->getPosition(), $formItem->getForm());
        $this->entityManager->flush();

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
        //FIXME Checkout artwork position service, there it works
        $positions = $form->getItems()->toArray();
        uasort($positions, function ($a, $b) {
            /** @var FormItem $a */
            /** @var FormItem $b */
            return ($a->getPosition() < $b->getPosition()) ? -1 : 1;
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
        $position = $this->rearrangeFormItems($formItem->getPosition(), $formItem->getForm());
        $formItem->setPosition($position);

        $this->entityManager->flush();
    }

    /**
     * Gets the form item in the given form at the given position
     *
     * @param string $slug
     * @param int $position
     * @return FormItem
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
}