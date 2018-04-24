<?php

namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class Repository
{
    public function genRepository(
        $className,
        $entityName,
        $repoInterface,
        $resource,
        $collection,
        $entity,
        $results,
        $entityInterface,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $class = $namespace->addClass($className);
        $class->setImplements([$repoInterface]);

        $class->addProperty('resource')
            ->setVisibility('private')->addComment('@var ' . $resource);
        $class->addProperty('collectionFactory')
            ->setVisibility('private')->addComment('@var ' . $collection . 'Factory');
        $class->addProperty('entityFactory')
            ->setVisibility('private')->addComment('@var ' . $entity . 'Factory');
        $class->addProperty('searchResultsFactory')
            ->setVisibility('private')->addComment('@var ' . $results . 'Factory');

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $resource . ' $resource')
            ->addComment('@param ' . $collection . 'Factory $collectionFactory')
            ->addComment('@param ' . $entity . 'Factory $entityFactory')
            ->addComment('@param ' . $results . 'Factory $searchResultsFactory')
            ->setVisibility('public');

        $construct->addParameter('resource')->setTypeHint($resource);
        $construct->addParameter('collectionFactory')->setTypeHint($collection . 'Factory');
        $construct->addParameter('entityFactory')->setTypeHint($entity . 'Factory');
        $construct->addParameter('searchResultsFactory')->setTypeHint($results . 'Factory');

        $construct->setBody('$this->resource = $resource;' . PHP_EOL
            . '$this->collectionFactory = $collectionFactory;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            . '$this->searchResultsFactory = $searchResultsFactory;');

        $getById = $class->addMethod('getById')
            ->addComment('@param int $entityId')
            ->addComment('@return ' . $entity)
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->setVisibility('public');

        $getById->addParameter('entityId');
        $getById->setBody('$entity = $this->entityFactory->create();' . PHP_EOL
            . '$this->resource->load($entity, $entityId);' . PHP_EOL
            . 'if (!$entity->getId()) {' . PHP_EOL
            . 'throw new \Magento\Framework\Exception\NoSuchEntityException(' . PHP_EOL
            . '        __(\'' . $entityName . ' with id "%1" does not exist.\', $entityId)' . PHP_EOL
            . '    );' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $entity;');

        $save = $class->addMethod('save')
            ->addComment('@param ' . $entityInterface . ' $entity')
            ->addComment('@return ' . $entity)
            ->addComment('@throws \Magento\Framework\Exception\CouldNotSaveException')
            ->setVisibility('public');

        $save->addParameter('entity')->setTypeHint($entityInterface);
        $save->setBody('try {' . PHP_EOL
            . '$this->resource->save($entity);' . PHP_EOL
            . '} catch (\Exception $exception) {' . PHP_EOL
            . 'throw new \Magento\Framework\Exception\CouldNotSaveException(' . PHP_EOL
            . '        __($exception->getMessage())' . PHP_EOL
            . '    );' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $entity;');

        $delete = $class->addMethod('delete')
            ->addComment('@param ' . $entityInterface . ' $entity')
            ->addComment('@return bool')
            ->addComment('@throws \Magento\Framework\Exception\CouldNotDeleteException')
            ->setVisibility('public');

        $delete->addParameter('entity')->setTypeHint($entityInterface);
        $delete->setBody('try {' . PHP_EOL
            . '$this->resource->delete($entity);' . PHP_EOL
            . '} catch (\Exception $exception) {' . PHP_EOL
            . 'throw new \Magento\Framework\Exception\CouldNotDeleteException(' . PHP_EOL
            . '        __($exception->getMessage())' . PHP_EOL
            . '    );' . PHP_EOL
            . '}' . PHP_EOL
            . 'return true;');

        $deleteById = $class->addMethod('deleteById')
            ->addComment('@param int $entityId')
            ->addComment('@return bool')
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->addComment('@throws \Magento\Framework\Exception\CouldNotDeleteException')
            ->setVisibility('public');

        $deleteById->addParameter('entityId');
        $deleteById->setBody('return $this->delete($this->getById($entityId));');

        return $namespace;
    }
}
