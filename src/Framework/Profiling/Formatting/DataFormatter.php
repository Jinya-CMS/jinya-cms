<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 03.11.18
 * Time: 00:25
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Component\VarDumper\Cloner\Data;

class DataFormatter implements DataFormatterInterface
{
    /**
     * Converts the associative array to a norma index array
     *
     * @param array $data
     * @return array
     */
    public function convertAssocToList(array $data): array
    {
        $keys = array_keys($data);
        $values = array_values($data);

        return array_map(function ($key, $value) {
            if ($value instanceof Data) {
                $dataValue = $this->format($value);
            } else {
                $dataValue = $value;
            }

            return [
                'key' => $key,
                'value' => $dataValue,
            ];
        }, $keys, $values);
    }

    /**
     * Formats the data object into an array
     *
     * @param Data $data
     * @return mixed
     */
    public function format(Data $data)
    {
        if (!is_array($data->getValue())) {
            return $data->getValue();
        }

        $result = [];

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $value = array_map([$this, 'format'], $item);
            } elseif ($item instanceof Data) {
                $value = $this->format($item);
            } else {
                $value = $item;
            }

            $result[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        return $result;
    }
}
