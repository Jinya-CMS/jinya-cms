<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 07:59
 */

namespace Jinya\Services\Form;


use Jinya\Entity\Form;
use Jinya\Entity\FormItem;

interface FormItemServiceInterface
{
    /**
     * Adds a new form item to the given form
     *
     * @param FormItem $formItem
     * @param Form $form
     */
    public function addItem(FormItem $formItem, Form $form): void;

    /**
     * Deletes the item at the given position
     *
     * @param Form $form
     * @param int $position
     */
    public function deleteItem(Form $form, int $position): void;

    /**
     * Gets all items for the given form
     *
     * @param string $formSlug
     * @return array
     */
    public function getItems(string $formSlug): array;

    /**
     * Updates the given form item
     *
     * @param FormItem $formItem
     */
    public function updateItem(FormItem $formItem): void;
}