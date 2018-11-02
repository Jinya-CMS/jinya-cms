<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 00:23
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Twig\Profiler\Profile;

class TwigDataCollectorFormatter implements TwigDataCollectorFormatterInterface
{

    /**
     * Formats the doctrine data into a profiling array
     *
     * @param TwigDataCollector $collector
     * @return array
     */
    public function format(TwigDataCollector $collector): array
    {
        return $this->formatTwigProfile($collector->getProfile());
    }

    private function formatTwigProfile(Profile $twigProfile): array
    {
        return [
            $twigProfile->getName() => [
                'memoryUsage' => $twigProfile->getMemoryUsage(),
                'peakMemoryUsage' => $twigProfile->getPeakMemoryUsage(),
                'duration' => $twigProfile->getDuration(),
                'type' => $twigProfile->getName(),
                'profiles' => array_map([$this, 'formatTwigProfile'], $twigProfile->getProfiles())
            ],
        ];
    }
}