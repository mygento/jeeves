<?php

namespace Mygento\Jeeves\Generators\Crud\Repositories;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Repository extends Common
{
    public function genRepository(
        string $className,
        string $print,
        string $repoInterface,
        string $resource,
        string $collection,
        string $results,
        string $entityInterface,
        string $rootNamespace,
        bool $withStore = false,
        string $phpVersion = PHP_VERSION,
    ): PhpNamespace {
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);
        $readonlyClass = $this->hasReadOnlyClass($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        $namespace->addUse('\Magento\Framework\Exception\NoSuchEntityException');
        $namespace->addUse('\Magento\Framework\Exception\CouldNotSaveException');
        $namespace->addUse('\Magento\Framework\Exception\CouldNotDeleteException');

        $class = $namespace->addClass($className);
        $class->setImplements([$repoInterface]);
        $class->setComment('@SuppressWarnings(PHPMD.CouplingBetweenObjects)');

        if ($readonlyClass) {
            $class->setReadOnly($readonlyClass);
        }

        if (!$constructorProp) {
            $rs = $class->addProperty('resource')
                ->setVisibility('private');
            $cf = $class->addProperty('collectionFactory')
                ->setVisibility('private');
            $ef = $class->addProperty('entityFactory')
                ->setVisibility('private');
            $sr = $class->addProperty('searchResultsFactory')
                ->setVisibility('private');

            $namespace->addUse($repoInterface);
            $namespace->addUse($collection . 'Factory');
            $namespace->addUse($entityInterface . 'Factory');
            $namespace->addUse($results . 'Factory');

            $rs->setType($resource);
            $cf->setType($collection . 'Factory');
            $ef->setType($entityInterface . 'Factory');
            $sr->setType($results . 'Factory');
        }

        $namespace->addUse($repoInterface);
        $namespace->addUse($collection . 'Factory');
        $namespace->addUse($entityInterface . 'Factory');
        $namespace->addUse($results . 'Factory');

        $construct = $class->addMethod('__construct')->setVisibility('public');

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('resource')
                ->setReadOnly($readonlyProp && !$readonlyClass)
                ->setPrivate()
                ->setType($resource);
            $construct
                ->addPromotedParameter('collectionFactory')
                ->setReadOnly($readonlyProp && !$readonlyClass)
                ->setPrivate()
                ->setType($collection . 'Factory');
            $construct
                ->addPromotedParameter('entityFactory')
                ->setReadOnly($readonlyProp && !$readonlyClass)
                ->setPrivate()
                ->setType($entityInterface . 'Factory');
            $construct
                ->addPromotedParameter('searchResultsFactory')
                ->setReadOnly($readonlyProp && !$readonlyClass)
                ->setPrivate()
                ->setType($results . 'Factory');
        } else {
            $construct
                ->addParameter('resource')
                ->setType($resource);
            $construct
                ->addParameter('collectionFactory')
                ->setType($collection . 'Factory');
            $construct
                ->addParameter('entityFactory')
                ->setType($entityInterface . 'Factory');
            $construct
                ->addParameter('searchResultsFactory')
                ->setType($results . 'Factory');
        }

        if ($withStore) {
            $namespace->addUse('Magento\Store\Model\StoreManagerInterface');

            if (!$constructorProp) {
                $sm = $class->addProperty('storeManager')->setPrivate();

                $sm->setType('\Magento\Store\Model\StoreManagerInterface');
            }

            if ($constructorProp) {
                $construct
                    ->addPromotedParameter('storeManager')
                    ->setReadOnly($readonlyProp && !$readonlyClass)
                    ->setPrivate()
                    ->setType('\Magento\Store\Model\StoreManagerInterface');
            } else {
                $construct
                    ->addParameter('storeManager')
                    ->setType('\Magento\Store\Model\StoreManagerInterface');
            }
        }

        if (!$constructorProp) {
            $cp = $class->addProperty('collectionProcessor')
                ->setPrivate();

            $cp->setType('\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        }

        if ($constructorProp) {
            $construct->addPromotedParameter('collectionProcessor')
                ->setReadOnly($readonlyProp && !$readonlyClass)
                ->setPrivate()
                ->setType('Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        } else {
            $construct->addParameter('collectionProcessor')
                ->setType('Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        }

        if (!$constructorProp) {
            $construct->setBody('$this->resource = $resource;' . PHP_EOL
                . '$this->collectionFactory = $collectionFactory;' . PHP_EOL
                . '$this->entityFactory = $entityFactory;' . PHP_EOL
                . '$this->searchResultsFactory = $searchResultsFactory;' . PHP_EOL
                . '$this->collectionProcessor = $collectionProcessor;'
                . ($withStore ? PHP_EOL . '$this->storeManager = $storeManager;' : ''));
        }

        $getById = $class->addMethod('getById')->setVisibility('public');
        $getByIdParam = $getById->addParameter('entityId');

        $namespace->addUse($entityInterface);
        $getById->addComment('@throws NoSuchEntityException');
        $getById->setReturnType($entityInterface);
        $getByIdParam->setType('int');

        $getById->setBody('$entity = $this->entityFactory->create();' . PHP_EOL
            . '$this->resource->load($entity, $entityId);' . PHP_EOL
            . 'if (!$entity->getId()) {' . PHP_EOL
            . '    throw new NoSuchEntityException(' . PHP_EOL
            . '        __(\'A ' . $print . ' with id "%1" does not exist\', $entityId)' . PHP_EOL
            . '    );' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $entity;');

        $save = $class->addMethod('save')->setVisibility('public');

        $save->addParameter('entity')->setType($entityInterface);

        $save->addComment('@throws CouldNotSaveException');
        $save->setReturnType($entityInterface);

        $save->addBody(
            ($withStore ? 'if (empty($entity->getStoreId())) {' . PHP_EOL
                . self::TAB . '$entity->setStoreId([$this->storeManager->getStore()->getId()]);' . PHP_EOL
                . '}' . PHP_EOL : '')
                . 'try {' . PHP_EOL
                . self::TAB . '$this->resource->save($entity);' . PHP_EOL
                . '} catch (\Exception $exception) {' . PHP_EOL
                . self::TAB . 'throw new CouldNotSaveException(' . PHP_EOL
                . self::TAB . self::TAB . '__(\'Could not save the ' . $print . '\'),' . PHP_EOL
                . self::TAB . self::TAB . '$exception' . PHP_EOL
                . self::TAB . ');' . PHP_EOL
                . '}' . PHP_EOL
                . 'return $entity;',
        );

        $delete = $class->addMethod('delete')->setVisibility('public');

        $delete->addParameter('entity')->setType($entityInterface);

        $delete->addComment('@throws CouldNotDeleteException');
        $delete->setReturnType('bool');

        $delete->setBody('try {' . PHP_EOL
            . self::TAB . '$this->resource->delete($entity);' . PHP_EOL
            . '} catch (\Exception $exception) {' . PHP_EOL
            . self::TAB . 'throw new CouldNotDeleteException(' . PHP_EOL
            . self::TAB . self::TAB . '__($exception->getMessage())' . PHP_EOL
            . self::TAB . ');' . PHP_EOL
            . '}' . PHP_EOL
            . 'return true;');

        $deleteById = $class->addMethod('deleteById')->setVisibility('public');

        $deleteByIdParam = $deleteById->addParameter('entityId');

        $deleteById->addComment('@throws NoSuchEntityException');
        $deleteById->addComment('@throws CouldNotDeleteException');
        $deleteById->setReturnType('bool');
        $deleteByIdParam->setType('int');

        $deleteById->setBody('return $this->delete($this->getById($entityId));');

        $getList = $class->addMethod('getList')

            ->setVisibility('public');

        $getList->addParameter('criteria')->setType('\Magento\Framework\Api\SearchCriteriaInterface');

        $namespace->addUse('\Magento\Framework\Api\SearchCriteriaInterface');
        $namespace->addUse($results);
        $getList->setReturnType($results);

        $getList->setBody('/** @var ' . $collection . ' $collection */' . PHP_EOL
            . '$collection = $this->collectionFactory->create();' . PHP_EOL . PHP_EOL
            . '$this->collectionProcessor->process($criteria, $collection);' . PHP_EOL . PHP_EOL
            . '/** @var ' . $namespace->simplifyName($results) . ' $searchResults */' . PHP_EOL
            . '$searchResults = $this->searchResultsFactory->create();' . PHP_EOL
            . '$searchResults->setSearchCriteria($criteria);' . PHP_EOL
            . '$searchResults->setItems($collection->getItems());' . PHP_EOL
            . '$searchResults->setTotalCount($collection->getSize());' . PHP_EOL
            . 'return $searchResults;');

        return $namespace;
    }
}
