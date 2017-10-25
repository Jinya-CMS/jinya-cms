<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 19:24
 */

namespace BackendBundle\Service\Database;


use Doctrine\ORM\EntityManager;

class SchemaTool implements SchemaToolInterface
{

    /** @var EntityManager */
    private $entityManager;

    /**
     * SchemaTool constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function updateSchema()
    {
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metadatas);
    }
}