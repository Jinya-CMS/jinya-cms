<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.12.2017
 * Time: 17:51
 */

namespace Jinya\Services\Form;

use Jinya\Entity\Form;
use Jinya\Services\Base\StaticContentServiceInterface;

interface FormServiceInterface extends StaticContentServiceInterface
{
    /**
     * Gets the specified @see Form by slug
     *
     * @param string $slug
     * @return Form
     */
    public function get(string $slug);

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Form[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array;

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @return int
     */
    public function countAll(string $keyword = ''): int;

    /**
     * Saves or updates the given @see Form
     *
     * @param Form $form
     * @return Form
     */
    public function saveOrUpdate($form);

    /**
     * Deletes the given @see Form
     *
     * @param Form $entity
     */
    public function delete($entity): void;

    /**
     * Updates the given field
     *
     * @param string $key
     * @param string $value
     * @param int $id
     */
    public function updateField(string $key, string $value, int $id);
}
