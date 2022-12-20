<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Models;
use Mygento\Jeeves\Generators\Crud\Repositories;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Repository extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateRepository(Entity $entity)
    {
        $generator = new Repositories\Repository();
        $filePath = $entity->getPath() . '/Model/';
        $fileName = $entity->getEntityName() . 'Repository';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genRepository(
                $fileName,
                implode(' ', [
                    $this->getConverter()->getEntityPrintName($entity->getModule()->getModule()),
                    $this->getConverter()->getEntityPrintName($entity->getName()),
                ]),
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $namePath . 'Model\\ResourceModel\\' . $entity->getEntityName(),
                $namePath . 'Model\\ResourceModel\\' . $entity->getEntityName() . '\\Collection',
                $namePath . 'Api\\Data\\' . $entity->getEntityName() . 'SearchResultsInterface',
                $namePath . 'Api\\Data\\' . $entity->getEntityName() . 'Interface',
                $entity->getNamespace(),
                $entity->withStore(),
                $entity->getPhpVersion()
            )
        );
    }

    public function generateSearchResults(Entity $entity)
    {
        $generator = new Repositories\Search();
        $filePath = $entity->getPath() . '/Model/';
        $fileName = $entity->getEntityName() . 'SearchResults';
        $namePath = '\\' . $entity->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelSearch(
                $fileName,
                $namePath . $entity->getEntityName() . 'SearchResultsInterface',
                $entity->getNamespace(),
                $entity->getPhpVersion()
            )
        );
    }

    public function generateWithStore(Entity $entity)
    {
        $this->genReadHandler($entity);
        $this->genSaveHandler($entity);
        $this->getRepoFilter($entity);
    }

    private function genReadHandler(Entity $entity)
    {
        $generator = new Models\Read();
        $filePath = $entity->getPath() . '/Model/ResourceModel/' . $entity->getEntityName() . '/Relation/Store/';
        $fileName = 'ReadHandler';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genReadHandler(
                $entity->getEntityName(),
                $namePath . 'Model\\ResourceModel\\' . $entity->getEntityName(),
                $entity->getNamespace() . '\Api\Data\\' . $entity->getEntityName() . 'Interface',
                $entity->getNamespace(),
                $entity->getPhpVersion()
            )
        );
    }

    private function genSaveHandler(Entity $entity)
    {
        $generator = new Models\Save();
        $filePath = $entity->getPath() . '/Model/ResourceModel/' . $entity->getEntityName() . '/Relation/Store/';
        $fileName = 'SaveHandler';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genSaveHandler(
                $entity->getEntityName(),
                $entity->getNamespace() . '\Api\Data\\' . $entity->getEntityName() . 'Interface',
                $namePath . 'Model\\ResourceModel\\' . $entity->getEntityName(),
                $entity->getNamespace(),
                $entity->getPhpVersion()
            )
        );
    }

    private function getRepoFilter(Entity $entity)
    {
        $generator = new Repositories\Filter();
        $filePath = $entity->getPath() . '/Model/SearchCriteria/';
        $fileName = $entity->getEntityName() . 'StoreFilter';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->getRepoFilter(
                $fileName,
                $entity->getNamespace() . '\Api\Data\\' . $entity->getEntityName() . 'Interface',
                $entity->getNamespace(),
                $entity->getPhpVersion()
            )
        );
    }
}
