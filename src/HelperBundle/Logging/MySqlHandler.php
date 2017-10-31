<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 15:32
 */

namespace HelperBundle\Logging;


use Doctrine\ORM\EntityManager;
use Exception;
use HelperBundle\Entity\LogMessage;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MySqlHandler extends AbstractProcessingHandler
{
    /** @var EntityManager */
    private $entityManager;

    /** @var Logger */
    private $logger;

    /**
     * MySqlHandler constructor.
     * @param EntityManager $entityManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, Logger $logger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
            $logEntry = new LogMessage();
            $logEntry->setMessage($record['message']);
            $logEntry->setLevel($record['level']);
            $logEntry->setLevelName($record['level_name']);
            $logEntry->setExtra($record['extra']);
            $logEntry->setContext($record['context']);

            $this->entityManager->persist($logEntry);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        }
    }
}