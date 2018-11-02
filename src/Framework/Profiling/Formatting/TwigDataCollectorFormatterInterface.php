<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 00:18
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;

interface TwigDataCollectorFormatterInterface
{
    /**
     * Formats the doctrine data into a profiling array
     *
     * @param TwigDataCollector $collector
     * @return array
     */
    public function format(TwigDataCollector $collector): array;
}
