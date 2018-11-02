<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 00:38
 */

namespace Jinya\Framework\Profiling\Formatting;

use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
use Symfony\Bridge\Twig\DataCollector\TwigDataCollector;
use Symfony\Component\HttpKernel\Profiler\Profile;

class ProfileFormatter implements ProfileFormatterInterface
{
    /** @var TwigDataCollectorFormatterInterface */
    private $twigFormatter;

    /** @var DoctrineDataCollectorFormatterInterface */
    private $doctrineFormatter;

    /**
     * ProfileFormatter constructor.
     * @param TwigDataCollectorFormatterInterface $twigFormatter
     * @param DoctrineDataCollectorFormatterInterface $doctrineFormatter
     */
    public function __construct(TwigDataCollectorFormatterInterface $twigFormatter, DoctrineDataCollectorFormatterInterface $doctrineFormatter)
    {
        $this->twigFormatter = $twigFormatter;
        $this->doctrineFormatter = $doctrineFormatter;
    }

    /**
     * Formats the given profile as array
     *
     * @param Profile $profile
     * @return array
     */
    public function format(Profile $profile): array
    {
        $relevantData = [];

        foreach ($profile->getCollectors() as $collector) {
            if ($collector instanceof DoctrineDataCollector) {
                $relevantData['doctrine'] = $this->doctrineFormatter->format($collector);
            } elseif ($collector instanceof TwigDataCollector) {
                $relevantData['twig'] = $this->twigFormatter->format($collector);
            }
        }

        return $relevantData;
    }
}