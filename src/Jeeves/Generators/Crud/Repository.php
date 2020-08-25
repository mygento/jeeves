<?php

namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class Repository extends Common
{
    public function getRepoFilter($className, $entityName, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\SearchCriteria');
        $namespace->addUse('Magento\Framework\Api\Filter');
        $namespace->addUse('Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface');
        $namespace->addUse('Magento\Framework\Data\Collection\AbstractDb');
        $class = $namespace->addClass($className);
        $class->setImplements(['Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface']);

        $apply = $class->addMethod('apply')->setVisibility('public')->addComment('@inheritDoc');
        $apply->addParameter('filter')->setType('Magento\Framework\Api\Filter');
        $apply->addParameter('collection')->setType('Magento\Framework\Data\Collection\AbstractDb');

        $apply->setBody(
            '$collection->addFilter(' . PHP_EOL
            . self::TAB . '\'store_id\',' . PHP_EOL
            . self::TAB . '[\'in\' => $filter->getValue()]' . PHP_EOL
            . ');' . PHP_EOL . PHP_EOL
            . 'return true;'
        );

        return $namespace;
    }

    public function genRepository(
        $className,
        $entityName,
        $repoInterface,
        $resource,
        $collection,
        $results,
        $entityInterface,
        $rootNamespace,
        $withStore = false
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface');
        $class = $namespace->addClass($className);
        $class->setImplements([$repoInterface]);
        $class->setComment('@SuppressWarnings(PHPMD.CouplingBetweenObjects)');

        $class->addProperty('resource')
            ->setVisibility('private')->addComment('@var ' . $resource);
        $class->addProperty('collectionFactory')
            ->setVisibility('private')->addComment('@var ' . $collection . 'Factory');
        $class->addProperty('entityFactory')
            ->setVisibility('private')->addComment('@var ' . $entityInterface . 'Factory');
        $class->addProperty('searchResultsFactory')
            ->setVisibility('private')->addComment('@var ' . $results . 'Factory');

        $construct = $class->addMethod('__construct')->setVisibility('public');
        $construct
            ->addComment('@param ' . $resource . ' $resource')
            ->addComment('@param ' . $collection . 'Factory $collectionFactory')
            ->addComment('@param ' . $entityInterface . 'Factory $entityFactory')
            ->addComment('@param ' . $results . 'Factory $searchResultsFactory');

        $construct->addParameter('resource')->setType($resource);
        $construct->addParameter('collectionFactory')->setType($collection . 'Factory');
        $construct->addParameter('entityFactory')->setType($entityInterface . 'Factory');
        $construct->addParameter('searchResultsFactory')->setType($results . 'Factory');

        if ($withStore) {
            $namespace->addUse('Magento\Store\Model\StoreManagerInterface');
            $class->addProperty('storeManager')
                ->setVisibility('private')
                ->addComment('@var StoreManagerInterface');
            $construct
                ->addComment('@param StoreManagerInterface $storeManager');
            $construct
                ->addParameter('storeManager')
                ->setType('\Magento\Store\Model\StoreManagerInterface');
        }
        $class->addProperty('collectionProcessor')
            ->setVisibility('private')->addComment('@var CollectionProcessorInterface');
        $construct->addComment('@param CollectionProcessorInterface|null $collectionProcessor');
        $construct->addParameter('collectionProcessor')
            ->setType('Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface')
            ->setDefaultValue(null);

        $construct->setBody('$this->resource = $resource;' . PHP_EOL
            . '$this->collectionFactory = $collectionFactory;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            . '$this->searchResultsFactory = $searchResultsFactory;' . PHP_EOL
            . '$this->collectionProcessor = $collectionProcessor;'
            . ($withStore ? PHP_EOL . '$this->storeManager = $storeManager;' : ''));

        $getById = $class->addMethod('getById')
            ->addComment('@param int $entityId')
            ->addComment('@return ' . $entityInterface)
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->setVisibility('public');

        $getById->addParameter('entityId');
        $getById->setBody('$entity = $this->entityFactory->create();' . PHP_EOL
            . '$this->resource->load($entity, $entityId);' . PHP_EOL
            . 'if (!$entity->getId()) {' . PHP_EOL
            . '    throw new \Magento\Framework\Exception\NoSuchEntityException(' . PHP_EOL
            . '        __(\'' . $entityName . ' with id "%1" does not exist.\', $entityId)' . PHP_EOL
            . '    );' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $entity;');

        $save = $class->addMethod('save')
            ->addComment('@param ' . $entityInterface . ' $entity')
            ->addComment('@return ' . $entityInterface)
            ->addComment('@throws \Magento\Framework\Exception\CouldNotSaveException')
            ->setVisibility('public');

        $save->addParameter('entity')->setType($entityInterface);
        $save->setBody(
            ($withStore ? 'if (empty($entity->getStoreId())) {' . PHP_EOL
            . self::TAB . '$entity->setStoreId($this->storeManager->getStore()->getId());' . PHP_EOL
            . '}' . PHP_EOL : '')
            . 'try {' . PHP_EOL
            . self::TAB . '$this->resource->save($entity);' . PHP_EOL
            . '} catch (\Exception $exception) {' . PHP_EOL
            . self::TAB . 'throw new \Magento\Framework\Exception\CouldNotSaveException(' . PHP_EOL
            . self::TAB . self::TAB . '__($exception->getMessage())' . PHP_EOL
            . self::TAB . ');' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $entity;'
        );

        $delete = $class->addMethod('delete')
            ->addComment('@param ' . $entityInterface . ' $entity')
            ->addComment('@return bool')
            ->addComment('@throws \Magento\Framework\Exception\CouldNotDeleteException')
            ->setVisibility('public');

        $delete->addParameter('entity')->setTypeHint($entityInterface);
        $delete->setBody('try {' . PHP_EOL
            . '$this->resource->delete($entity);' . PHP_EOL
            . '} catch (\Exception $exception) {' . PHP_EOL
            . '    throw new \Magento\Framework\Exception\CouldNotDeleteException(' . PHP_EOL
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

        $getList = $class->addMethod('getList')
            ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $criteria')
            ->addComment('@return ' . $results)
            ->setVisibility('public');

        $getList->addParameter('criteria')->setTypeHint('\Magento\Framework\Api\SearchCriteriaInterface');
        $getList->setBody('/** @var ' . $collection . ' $collection */' . PHP_EOL
        . '$collection = $this->collectionFactory->create();' . PHP_EOL . PHP_EOL
        . '$this->collectionProcessor->process($criteria, $collection);' . PHP_EOL . PHP_EOL
        . '/** @var ' . $results . ' $searchResults */' . PHP_EOL
        . '$searchResults = $this->searchResultsFactory->create();' . PHP_EOL
        . '$searchResults->setSearchCriteria($criteria);' . PHP_EOL
        . '$searchResults->setItems($collection->getItems());' . PHP_EOL
        . '$searchResults->setTotalCount($collection->getSize());' . PHP_EOL
        . 'return $searchResults;');

        return $namespace;
    }
}
