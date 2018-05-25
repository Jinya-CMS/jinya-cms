<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 07:59.
 */

namespace Jinya\Services\Form;

use Jinya\Entity\Form;
use Jinya\Entity\FormItem;

interface FormItemServiceInterface
{
    /**
     * Adds a new form item to the given form.
     *
     * @param FormItem $formItem
     */
    public function addItem(FormItem $formItem): void;

    /**
     * Deletes the item at the given position.
     *
     * @param Form $form
     * @param int  $position
     */
    public function deleteItem(Form $form, int $position): void;

    /**
     * Gets all items for the given form.
     *
     * @param string $formSlug
     *
     * @return array
     */
    public function getItems(string $formSlug): array;

    /**
     * Updates the given form item.
     *
     * @param FormItem $formItem
     */
    public function updateItem(FormItem $formItem): void;

    /**
     * Updates the position.
     *
     * @param string $formSlug
     * @param int    $oldPosition
     * @param int    $newPosition
     */
    public function updatePosition(string $formSlug, int $oldPosition, int $newPosition): void;

    /**
     * Gets the form item in the given form at the given position.
     *
     * @param string $slug
     * @param int    $position
     *
     * @return FormItem
     */
    public function getItem(string $slug, int $position): FormItem;
}
