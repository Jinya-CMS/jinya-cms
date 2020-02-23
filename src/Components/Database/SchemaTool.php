<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 19:24.
 */

namespace Jinya\Components\Database;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\ToolsException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class SchemaTool implements SchemaToolInterface
{
    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var KernelInterface */
    private KernelInterface $kernel;

    /**
     * SchemaTool constructor.
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
    }

    /**
     * Creates the database schema
     * @throws ToolsException
     */
    public function createSchema(): void
    {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function migrateSchema(): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:migrations:migrate',
        ]);

        $output = new NullOutput();
        $application->run($input, $output);
    }
}
