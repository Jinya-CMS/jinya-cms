<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 00:15
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector;

interface DoctrineDataCollectorFormatterInterface
{
    /**
     * Formats the doctrine data into a profiling array
     *
     * @param DoctrineDataCollector $collector
     * @return array
     */
    public function format(DoctrineDataCollector $collector): array;
}