<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 16:26
 */

namespace Jinya\EventSubscriber\Monolog;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestProcessor
{
    /** @var RequestStack */
    private RequestStack $request;

    /**
     * RequestProcessor constructor.
     * @param RequestStack $request
     */
    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function processRecord(array $record): array
    {
        $request = $this->request->getCurrentRequest();
        $record['request'] = $request;

        return $record;
    }
}
