<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 23:30
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;

class RequestDataCollectorFormatter implements RequestDataCollectorFormatterInterface
{

    /** @var callable $formatCallable */
    private $formatCallable;

    /** @var DataFormatterInterface */
    private $dataFormatter;

    /**
     * RequestDataCollectorFormatter constructor.
     * @param DataFormatterInterface $dataFormatter
     */
    public function __construct(DataFormatterInterface $dataFormatter)
    {
        $this->dataFormatter = $dataFormatter;
        $this->formatCallable = [$this->dataFormatter, 'format'];
    }

    /**
     * Formats the data of a request
     *
     * @param RequestDataCollector $collector
     * @return array
     */
    public function format(RequestDataCollector $collector): array
    {
        return [
            'request' => [
                'attributes' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getRequestAttributes()->all())),
                'headers' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getRequestHeaders()->all())),
                'cookies' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getRequestCookies()->all())),
                'request' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getRequestRequest()->all())),
                'content' => $collector->getContent(),
            ],
            'response' => [
                'cookies' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getResponseCookies()->all())),
                'headers' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getResponseHeaders()->all())),
            ],
            'environment' => [
                'server' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getRequestServer()->all())),
                'dotenv' => $this->dataFormatter->convertAssocToList(array_map($this->formatCallable, $collector->getDotenvVars()->all())),
            ]
        ];
    }
}