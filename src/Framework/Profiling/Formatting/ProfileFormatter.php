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
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;
use Symfony\Component\HttpKernel\Profiler\Profile;

class ProfileFormatter implements ProfileFormatterInterface
{
    /** @var TwigDataCollectorFormatterInterface */
    private $twigFormatter;

    /** @var DoctrineDataCollectorFormatterInterface */
    private $doctrineFormatter;

    /** @var RequestDataCollectorFormatterInterface */
    private $requestFormatter;

    /**
     * ProfileFormatter constructor.
     * @param TwigDataCollectorFormatterInterface $twigFormatter
     * @param DoctrineDataCollectorFormatterInterface $doctrineFormatter
     * @param RequestDataCollectorFormatterInterface $requestFormatter
     */
    public function __construct(TwigDataCollectorFormatterInterface $twigFormatter, DoctrineDataCollectorFormatterInterface $doctrineFormatter, RequestDataCollectorFormatterInterface $requestFormatter)
    {
        $this->twigFormatter = $twigFormatter;
        $this->doctrineFormatter = $doctrineFormatter;
        $this->requestFormatter = $requestFormatter;
    }

    /**
     * Formats the given profile as array
     *
     * @param Profile $profile
     * @return array
     */
    public function format(Profile $profile): array
    {
        /** @var RequestDataCollector $requestCollector */
        $requestCollector = $profile->getCollector('request');
        /** @var DoctrineDataCollector $doctrineCollector */
        $doctrineCollector = $profile->getCollector('db');
        /** @var TwigDataCollector $twigCollector */
        $twigCollector = $profile->getCollector('twig');

        return [
            'request' => $this->requestFormatter->format($requestCollector),
            'twig' => $this->twigFormatter->format($twigCollector),
            'doctrine' => $this->doctrineFormatter->format($doctrineCollector),
        ];
    }
}