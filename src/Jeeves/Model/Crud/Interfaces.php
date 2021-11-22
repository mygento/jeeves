<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Interfaces as Generators;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Interfaces extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateInterfaces(Entity $entity)
    {
        $this->genModelInterface($entity);
        $this->genModelRepositoryInterface($entity);
        $this->genModelSearchInterface($entity);
    }

    private function genModelInterface(Entity $entity)
    {
        $generator = new Generators\Model();
        $filePath = $entity->getPath() . '/Api/Data/';
        $fileName = $entity->getEntityName() . 'Interface';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelInterface(
                $fileName,
                $entity->getPrimaryKey(),
                $entity->getNamespace(),
                $entity->getCacheTag(),
                $entity->getColumns(),
                $entity->withStore(),
                $entity->hasTypehint()
            )
        );
    }

    private function genModelRepositoryInterface(Entity $entity)
    {
        $generator = new Generators\Repository();
        $filePath = $entity->getPath() . '/Api/';
        $fileName = $entity->getEntityName() . 'RepositoryInterface';
        $namePath = '\\' . $entity->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelRepositoryInterface(
                $namePath . $entity->getEntityName() . 'Interface',
                $namePath . $entity->getEntityName() . 'SearchResultsInterface',
                $fileName,
                $this->getConverter()->getEntityPrintName($entity->getName()),
                $entity->getNamespace(),
                $entity->hasTypehint()
            )
        );
    }

    private function genModelSearchInterface(Entity $entity)
    {
        $generator = new Generators\Search();
        $filePath = $entity->getPath() . '/Api/Data/';
        $fileName = $entity->getEntityName() . 'SearchResultsInterface';
        $namePath = '\\' . $entity->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelSearchInterface(
                $entity->getEntityName(),
                $fileName,
                $this->getConverter()->getEntityPrintName($entity->getName()),
                $namePath . $entity->getEntityName() . 'Interface',
                $entity->getNamespace(),
                $entity->hasTypehint()
            )
        );
    }
}
