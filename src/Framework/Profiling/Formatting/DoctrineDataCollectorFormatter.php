<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 00:41
 */

namespace Jinya\Framework\Profiling\Formatting;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ReflectionClass;
use Symfony\Bridge\Doctrine\DataCollector\DoctrineDataCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\VarDumper\Cloner\Data;

class DoctrineDataCollectorFormatter implements DoctrineDataCollectorFormatterInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var DataFormatterInterface */
    private $dataFormatter;

    /**
     * DoctrineDataCollectorFormatter constructor.
     * @param ContainerInterface $container
     * @param DataFormatterInterface $dataFormatter
     */
    public function __construct(ContainerInterface $container, DataFormatterInterface $dataFormatter)
    {
        $this->container = $container;
        $this->dataFormatter = $dataFormatter;
    }

    /**
     * Formats the doctrine data into a profiling array
     *
     * @param DoctrineDataCollector $collector
     * @return array
     */
    public function format(DoctrineDataCollector $collector): array
    {
        $queries = $collector->getQueries();
        $connections = array_map([$this->container, 'get'], $collector->getConnections());
        $managers = array_map([$this->container, 'get'], $collector->getManagers());

        $result = [
            'queryCount' => $collector->getQueryCount(),
            'time' => $collector->getTime(),
            'queries' => [],
            'connections' => array_map([$this, 'formatConnection'], $connections),
            'managers' => array_map([$this, 'formatManager'], $managers),
        ];

        foreach ($queries as $connection => $query) {
            $result['queries'][$connection] = array_values(array_map([$this, 'formatQuery'], $query));
        }

        return $result;
    }

    private function formatQuery(array $query): array
    {
        /** @var Data $params */
        $params = $query['params'];

        return [
            'sql' => $query['sql'],
            'executionTime' => $query['executionMS'],
            'params' => array_values(array_map([$this, 'formatParam'], $params->getValue())),
        ];
    }

    private function formatParam(Data $param)
    {
        if (is_array($param->getValue())) {
            return array_map([$this, 'formatParam'], $param->getValue());
        }

        return $param->getValue();
    }

    private function formatConnection(Connection $connection): array
    {
        try {
            $connectionReflection = new ReflectionClass(get_class($connection));
            $pdoConnectionProperty = $connectionReflection->getProperty('_conn');
            $pdoConnectionProperty->setAccessible(true);

            /** @var PDOConnection $pdoConnection */
            $pdoConnection = $pdoConnectionProperty->getValue($connection);
            $clientVersion = $pdoConnection->getAttribute(PDOConnection::ATTR_CLIENT_VERSION);
            $driverName = $pdoConnection->getAttribute(PDOConnection::ATTR_DRIVER_NAME);
            $serverInfo = $pdoConnection->getAttribute(PDOConnection::ATTR_SERVER_INFO);
            $serverVersion = $pdoConnection->getAttribute(PDOConnection::ATTR_SERVER_VERSION);
        } catch (\ReflectionException $e) {
            $clientVersion = '';
            $driverName = '';
            $serverInfo = '';
            $serverVersion = '';
        }

        return [
            'database' => $connection->getDatabase(),
            'clientVersion' => $clientVersion,
            'driverName' => $driverName,
            'serverInfo' => $serverInfo,
            'serverVersion' => $serverVersion,
            'params' => $connection->getParams(),
        ];
    }

    private function formatManager(EntityManagerInterface $entityManager): array
    {
        try {
            $metadataFactory = $entityManager->getMetadataFactory();
            $metadataReflection = new ReflectionClass(get_class($metadataFactory));
            $driverProperty = $metadataReflection->getProperty('driver');
            $driverProperty->setAccessible(true);

            /** @var MappingDriverChain $mappingDriverChain */
            $mappingDriverChain = $driverProperty->getValue($metadataFactory);
            $mappingDrivers = $mappingDriverChain->getDrivers();
        } catch (\ReflectionException $e) {
            $mappingDrivers = [];
        }

        $configuration = $entityManager->getConfiguration();

        try {
            $repositoryFactory = $configuration->getRepositoryFactory();
            $metadataReflection = new ReflectionClass(get_class($repositoryFactory));
            $managedRepositoriesProperty = $metadataReflection->getProperty('managedRepositories');
            $managedRepositoriesProperty->setAccessible(true);

            /** @var EntityRepository[] $managedRepositories */
            $managedRepositories = $managedRepositoriesProperty->getValue($repositoryFactory);
        } catch (\ReflectionException $e) {
            $managedRepositories = [];
        }

        return [
            'configuration' => [
                'proxyDir' => $configuration->getProxyDir(),
                'proxyNamespace' => $configuration->getProxyNamespace(),
                'entityNamespaces' => $this->dataFormatter->convertAssocToList($configuration->getEntityNamespaces()),
                'repositoryFactory' => $this->dataFormatter->convertAssocToList(array_map(function (EntityRepository $repository) {
                    return $repository->getClassName();
                }, $managedRepositories)),
            ],
            'mappingDrivers' => array_map(function (MappingDriver $driver) {
                return ['classes' => $driver->getAllClassNames()];
            }, $mappingDrivers),
        ];
    }
}
