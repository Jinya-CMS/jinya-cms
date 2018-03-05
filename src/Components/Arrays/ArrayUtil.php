<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.03.2018
 * Time: 19:48
 */

namespace Jinya\Components\Arrays;


use function explode;

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
    public function setArrayValueByPath(array $data, string $path, $newValue, string $delimiter = '/'): array
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
    public function buildArrayFromPath(string $path, string $delimiter = '~'): array
    {
        $keys = explode($delimiter, $path);
        $buildArray = function (array &$input, int $idx) use (&$buildArray, $keys) {
            if (count($keys) > $idx) {
                $input[$keys[$idx]] = [];

                $buildArray($input[$keys[$idx]], $idx + 1);
            }
        };
        $data = [];
        $buildArray($data, 0);

        return $data;
    }
}