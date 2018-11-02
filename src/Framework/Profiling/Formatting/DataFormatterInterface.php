<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 03.11.18
 * Time: 00:24
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Component\VarDumper\Cloner\Data;

interface DataFormatterInterface
{
    /**
     * Formats the data object into an array
     *
     * @param Data $data
     * @return mixed
     */
    public function format(Data $data);

    /**
     * Converts the associative array to a norma index array
     *
     * @param array $data
     * @return array
     */
    public function convertAssocToList(array $data): array;
}