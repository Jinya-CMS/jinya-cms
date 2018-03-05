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
     * @param array $data
     * @param string $path
     * @param $newValue
     * @param string $delimiter
     * @return array
     */
    public function setArrayValueByPath(array $data, string $path, $newValue, string $delimiter = '~'): array;

    /**
     * Builds a new empty array from the given path
     *
     * @param string $path
     * @param string $delimiter
     * @return array
     */
    public function buildArrayFromPath(string $path, string $delimiter = '~'): array;
}