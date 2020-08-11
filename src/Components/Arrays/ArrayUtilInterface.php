<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 19:46
 */

namespace Jinya\Components\Arrays;

interface ArrayUtilInterface
{
    /**
     * Sets an array value based on the path
     *
     * @param $newValue
     */
    public function setArrayValueByPath(array $data, string $path, $newValue, string $delimiter = '.'): array;

    /**
     * Builds a new empty array from the given path
     */
    public function buildArrayFromPath(string $path, string $delimiter = '.'): array;

    /**
     * Removes the value behind the given path
     */
    public function removeArrayValueByPath(array $data, string $path, string $delimiter = '.'): array;

    /**
     * Gets the value behind the given path
     *
     * @return mixed
     */
    public function getArrayValueByPath(array $data, string $path, string $delimiter = '.');

    /**
     * Removes all empty entries recursively
     */
    public function removeEmptyRecursive(array $data): array;
}
