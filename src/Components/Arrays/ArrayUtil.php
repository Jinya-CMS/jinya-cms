<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 19:48
 */

namespace Jinya\Components\Arrays;

use function explode;
use function is_array;

class ArrayUtil implements ArrayUtilInterface
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
    public function setArrayValueByPath(array $data, string $path, $newValue, string $delimiter = '.'): array
    {
        $keys = explode($delimiter, $path);
        $temp = &$data;
        foreach ($keys as $key) {
            $temp = &$temp[$key];
        }

        $temp = $newValue;
        unset($temp);

        return $data;
    }

    /**
     * Builds a new empty array from the given path
     *
     * @param string $path
     * @param string $delimiter
     * @return array
     */
    public function buildArrayFromPath(string $path, string $delimiter = '.'): array
    {
        $keys = explode($delimiter, $path);
        $buildArray = static function (array &$input, int $idx) use (&$buildArray, $keys) {
            if (count($keys) > $idx) {
                $input[$keys[$idx]] = [];

                $buildArray($input[$keys[$idx]], $idx + 1);
            }
        };
        $data = [];
        $buildArray($data, 0);

        return $data;
    }

    /**
     * @param array $data
     * @param string $path
     * @param string $delimiter
     * @return array
     */
    public function removeArrayValueByPath(array $data, string $path, string $delimiter = '.'): array
    {
        $keys = explode($delimiter, $path);
        $temp = &$data;
        $prevEl = null;
        $key = '';
        foreach ($keys as $key) {
            $prevEl = &$temp;
            $temp = &$temp[$key];
        }

        if (null !== $prevEl) {
            unset($prevEl[$key]);
        }

        return $data;
    }

    /**
     * Gets the value behind the given path
     *
     * @param array $data
     * @param string $path
     * @param string $delimiter
     * @return mixed
     */
    public function getArrayValueByPath(array $data, string $path, string $delimiter = '.')
    {
        return array_reduce(explode('.', $path), static function ($acc, $val) {
            return $acc[$val];
        }, $data);
    }

    /**
     * Removes all empty entries recursively
     *
     * @param array $data
     * @return array
     */
    public function removeEmptyRecursive(array $data): array
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->removeEmptyRecursive($value);
            }
        }

        return array_filter($data, static function ($value) {
            return false === $value || !empty($value);
        });
    }
}
