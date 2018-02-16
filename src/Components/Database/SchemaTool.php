<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 19:24.
 */

namespace Jinya\Components\Database;

use Doctrine\ORM\EntityManagerInterface;

class SchemaTool implements SchemaToolInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * SchemaTool constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @inheritdoc
     */
    public function updateSchema(): void
    {
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metadatas);
    }
}
