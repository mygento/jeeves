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
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        $namespace->addUse('\Magento\Framework\Exception\NoSuchEntityException');
        $namespace->addUse('\Magento\Framework\Exception\CouldNotSaveException');
        $namespace->addUse('\Magento\Framework\Exception\CouldNotDeleteException');

        $class = $namespace->addClass($className);
        $class->setImplements([$repoInterface]);
        $class->setComment('@SuppressWarnings(PHPMD.CouplingBetweenObjects)');

        $rs = $class->addProperty('resource')
            ->setVisibility('private');
        $cf = $class->addProperty('collectionFactory')
            ->setVisibility('private');
        $ef = $class->addProperty('entityFactory')
            ->setVisibility('private');
        $sr = $class->addProperty('searchResultsFactory')
            ->setVisibility('private');

        if ($typehint) {
            $namespace->addUse($repoInterface);
            $namespace->addUse($collection . 'Factory');
            $namespace->addUse($entityInterface . 'Factory');
            $namespace->addUse($results . 'Factory');

            $rs->setType($resource);
            $cf->setType($collection . 'Factory');
            $ef->setType($entityInterface . 'Factory');
            $sr->setType($results . 'Factory');
        } else {
            $rs->addComment('@var ' . $resource);
            $cf->addComment('@var ' . $collection . 'Factory');
            $ef->addComment('@var ' . $entityInterface . 'Factory');
            $sr->addComment('@var ' . $results . 'Factory');
        }

        $construct = $class->addMethod('__construct')->setVisibility('public');
        if (!$typehint) {
            $construct
                ->addComment('@param ' . $resource . ' $resource')
                ->addComment('@param ' . $collection . 'Factory $collectionFactory')
                ->addComment('@param ' . $entityInterface . 'Factory $entityFactory')
                ->addComment('@param ' . $results . 'Factory $searchResultsFactory');
        }

        $construct->addParameter('resource')->setType($resource);
        $construct->addParameter('collectionFactory')->setType($collection . 'Factory');
        $construct->addParameter('entityFactory')->setType($entityInterface . 'Factory');
        $construct->addParameter('searchResultsFactory')->setType($results . 'Factory');

        if ($withStore) {
            $namespace->addUse('Magento\Store\Model\StoreManagerInterface');
            $sm = $class->addProperty('storeManager')
                ->setVisibility('private');

            $construct
                ->addParameter('storeManager')
                ->setType('\Magento\Store\Model\StoreManagerInterface');

            if ($typehint) {
                $sm->setType('\Magento\Store\Model\StoreManagerInterface');
            } else {
                $construct
                    ->addComment('@param StoreManagerInterface $storeManager');
                $sm->addComment('@var StoreManagerInterface');
            }
        }

        $cp = $class->addProperty('collectionProcessor')
            ->setVisibility('private');

        $construct->addParameter('collectionProcessor')
            ->setType('Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');

        if ($typehint) {
            $cp->setType('\Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        } else {
            $construct->addComment('@param CollectionProcessorInterface $collectionProcessor');
            $cp->addComment('@var CollectionProcessorInterface');
        }

        $construct->setBody('$this->resource = $resource;' . PHP_EOL
            . '$this->collectionFactory = $collectionFactory;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            . '$this->searchResultsFactory = $searchResultsFactory;' . PHP_EOL
            . '$this->collectionProcessor = $collectionProcessor;'
            . ($withStore ? PHP_EOL . '$this->storeManager = $storeManager;' : ''));

        $getById = $class->addMethod('getById')->setVisibility('public');
        $getByIdParam = $getById->addParameter('entityId');

        if ($typehint) {
            $namespace->addUse($entityInterface);
            $getById->addComment('@throws NoSuchEntityException');
            $getById->setReturnType($entityInterface);
            $getByIdParam->setType('int');
        } else {
            $getById
                ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
                ->addComment('@param int $entityId')
                ->addComment('@return ' . $entityInterface);
        }

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

        if ($typehint) {
            $save->addComment('@throws CouldNotSaveException');
            $save->setReturnType($entityInterface);
        } else {
            $save
                ->addComment('@throws \Magento\Framework\Exception\CouldNotSaveException')
                ->addComment('@param ' . $entityInterface . ' $entity')
                ->addComment('@return ' . $entityInterface);
        }

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
            . 'return $entity;'
        );

        $delete = $class->addMethod('delete')->setVisibility('public');

        $delete->addParameter('entity')->setTypeHint($entityInterface);

        if ($typehint) {
            $delete->addComment('@throws CouldNotDeleteException');
            $delete->setReturnType('bool');
        } else {
            $delete
                ->addComment('@throws \Magento\Framework\Exception\CouldNotDeleteException')
                ->addComment('@param ' . $entityInterface . ' $entity')
                ->addComment('@return bool');
        }

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

        if ($typehint) {
            $deleteById->addComment('@throws NoSuchEntityException');
            $deleteById->addComment('@throws CouldNotDeleteException');
            $deleteById->setReturnType('bool');
            $deleteByIdParam->setType('int');
        } else {
            $deleteById
                ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
                ->addComment('@throws \Magento\Framework\Exception\CouldNotDeleteException')
                ->addComment('@param int $entityId')
                ->addComment('@return bool');
        }

        $deleteById->setBody('return $this->delete($this->getById($entityId));');

        $getList = $class->addMethod('getList')

            ->setVisibility('public');

        $getList->addParameter('criteria')->setTypeHint('\Magento\Framework\Api\SearchCriteriaInterface');

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Api\SearchCriteriaInterface');
            $namespace->addUse($results);
            $getList->setReturnType($results);
        } else {
            $getList
                ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $criteria')
                ->addComment('@return ' . $results);
        }

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
