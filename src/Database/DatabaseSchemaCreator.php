<?php

namespace ResourceBundle\Database;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Towersystems\Resource\Metadata\RegistryInterface;

class DatabaseSchemaCreator
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RegistryInterface
     */
    private $resourceRegistry;

    public function __construct(
        EntityManagerInterface $entityManager,
        RegistryInterface $resourceRegistry
    ) {
        $this->entityManager = $entityManager;
        $this->resourceRegistry = $resourceRegistry;
    }

    public function createSchema()
    {
        $classes = $this->getRegisteredClasses();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema($classes);
    }

    private function getRegisteredClasses()
    {
        $entityManager = $this->entityManager;
        return array_map(function ($resourceMetadata) use ($entityManager) {
            $className = $resourceMetadata->getClass('model');
            return $entityManager->getClassMetadata($className);
        }, $this->resourceRegistry->getAll());
    }
}
