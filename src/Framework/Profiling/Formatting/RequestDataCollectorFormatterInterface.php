<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 23:22
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;

interface RequestDataCollectorFormatterInterface
{
    /**
     * Formats the data of a request
     *
     * @param RequestDataCollector $collector
     * @return array
     */
    public function format(RequestDataCollector $collector): array;
}
