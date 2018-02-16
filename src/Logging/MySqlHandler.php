<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 31.10.2017
 * Time: 15:32
 */

namespace Jinya\Logging;


use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Jinya\Entity\LogEntry;
use Monolog\Handler\AbstractProcessingHandler;
use Psr\Log\LoggerInterface;

class MySqlHandler extends AbstractProcessingHandler
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * MySqlHandler constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    public function handle(array $record)
    {
        return true;
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
            $logEntry = new LogEntry();
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