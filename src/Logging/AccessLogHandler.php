<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 16:19
 */

namespace Jinya\Logging;


use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Jinya\Entity\AccessLogEntry;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\HttpFoundation\Request;

class AccessLogHandler extends AbstractProcessingHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * AccessLogHandler constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        try {
            /** @var Request $request */
            $request = $record['request'];
            $accessLogEntry = new AccessLogEntry();
            $accessLogEntry->setClientIp($request->getClientIp());
            $accessLogEntry->setMethod($request->getMethod());
            $accessLogEntry->setQueryString($request->getQueryString() ?? '');
            $accessLogEntry->setRequest($request->request->all());
            $accessLogEntry->setUri($request->getUri());
            $accessLogEntry->setUserAgent($_SERVER['HTTP_USER_AGENT']);

            $this->entityManager->persist($accessLogEntry);
            $this->entityManager->flush();
        } catch (Exception $exception) {
        }
    }
}