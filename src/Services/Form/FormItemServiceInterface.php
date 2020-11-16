<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 07:59
 */

namespace Jinya\Services\Form;

use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;

interface FormItemServiceInterface
{
    /**
     * Adds a new form item to the given form
     */
    public function addItem(FormItem $formItem): void;

    /**
     * Deletes the item at the given position
     */
    public function deleteItem(Form $form, int $position): void;

    /**
     * Gets all items for the given form
     */
    public function getItems(string $formSlug): array;

    /**
     * Updates the given form item
     */
    public function updateItem(FormItem $formItem): void;

    /**
     * Updates the position
     */
    public function updatePosition(string $formSlug, int $oldPosition, int $newPosition): void;

    /**
     * Gets the form item in the given form at the given position
     */
    public function getItem(string $slug, int $position): FormItem;
}
