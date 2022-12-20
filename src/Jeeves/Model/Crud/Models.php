<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Models as Generators;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Models extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateModels(Entity $entity)
    {
        $this->genModel($entity);
        $this->genResourceModel($entity);
        $this->genResourceCollection($entity);
    }

    private function genModel(Entity $entity)
    {
        $generator = new Generators\Model();
        $filePath = $entity->getPath() . '/Model/';
        $fileName = $entity->getEntityName();
        $namePath = '\\' . $entity->getNamespace() . '\\Api\\Data\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModel(
                $fileName,
                $namePath . $entity->getEntityName() . 'Interface',
                'ResourceModel' . '\\' . $entity->getEntityName(),
                $entity->getNamespace(),
                $entity->getEventName($entity->getName()),
                $entity->getCacheTag(),
                $entity->getColumns(),
                $entity->withStore(),
                $entity->getPhpVersion()
            )
        );
    }

    private function genResourceModel(Entity $entity)
    {
        $generator = new Generators\Resource();
        $filePath = $entity->getPath() . '/Model/ResourceModel/';
        $fileName = $entity->getEntityName();
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceModel(
                $fileName,
                $entity->getTablename(),
                $entity->getPrimaryKey(),
                $entity->getNamespace(),
                $entity->getEntityName() . 'Interface',
                $entity->withStore(),
                $entity->getPhpVersion()
            )
        );
    }

    private function genResourceCollection(Entity $entity)
    {
        $generator = new Generators\Collection();
        $filePath = $entity->getPath() . '/Model/ResourceModel/' . $entity->getEntityName() . '/';
        $fileName = 'Collection';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceCollection(
                $entity->getEntityName(),
                '\\' . $entity->getNamespace() . '\\Model' . '\\' . $entity->getEntityName(),
                '\\' . $entity->getNamespace() . '\\Model\\ResourceModel' . '\\' . $entity->getEntityName(),
                $entity->getNamespace(),
                $entity->getEntityName() . 'Interface',
                $entity->getPrimaryKey(),
                $entity->withStore(),
                $entity->getPhpVersion()
            )
        );
    }
}
