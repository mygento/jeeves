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
        $namespace->addUse('Magento\Framework\Api\SortOrder');
        $namespace->addUse('Magento\Framework\Data\Collection');
        $class = $namespace->addClass($className);
        $class->setImplements([$repoInterface]);
        $class->setComment('@SuppressWarnings(PHPMD.CouplingBetweenObjects)');

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

        $getList = $class->addMethod('getList')
            ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $criteria')
            ->addComment('@return ' . $results)
            ->setVisibility('public');

        $getList->addParameter('criteria')->setTypeHint('\Magento\Framework\Api\SearchCriteriaInterface');
        $getList->setBody('/** @var ' . $collection . ' $collection */' . PHP_EOL
        . '$collection = $this->collectionFactory->create();' . PHP_EOL
        . 'foreach ($criteria->getFilterGroups() as $filterGroup) {' . PHP_EOL
        . '    $fields = [];' . PHP_EOL
        . '    $conditions = [];' . PHP_EOL
        . '    foreach ($filterGroup->getFilters() as $filter) {' . PHP_EOL
        . '        $condition = $filter->getConditionType() ? $filter->getConditionType() : \'eq\';' . PHP_EOL
        . '        $fields[] = $filter->getField();' . PHP_EOL
        . '        $conditions[] = [$condition => $filter->getValue()];' . PHP_EOL
        . '    }' . PHP_EOL
        . '    if ($fields) {' . PHP_EOL
        . '        $collection->addFieldToFilter($fields, $conditions);' . PHP_EOL
        . '    }' . PHP_EOL
        . '}' . PHP_EOL
        . '$sortOrders = $criteria->getSortOrders();' . PHP_EOL
        . '$sortAsc = SortOrder::SORT_ASC;'
        . '$orderAsc = Collection::SORT_ORDER_ASC;'
        . '$orderDesc = Collection::SORT_ORDER_DESC;'
        . 'if ($sortOrders) {' . PHP_EOL
        . '    /** @var SortOrder $sortOrder */' . PHP_EOL
        . '    foreach ($sortOrders as $sortOrder) {' . PHP_EOL
        . '        $collection->addOrder(' . PHP_EOL
        . '            $sortOrder->getField(),' . PHP_EOL
        . '            ($sortOrder->getDirection() == $sortAsc) ? $orderAsc: $orderDesc' . PHP_EOL
        . '        );' . PHP_EOL
        . '    }' . PHP_EOL
        . '}' . PHP_EOL
        . '$collection->setCurPage($criteria->getCurrentPage());' . PHP_EOL
        . '$collection->setPageSize($criteria->getPageSize());' . PHP_EOL . PHP_EOL
        . '/** @var ' . $results . ' $searchResults */' . PHP_EOL
        . '$searchResults = $this->searchResultsFactory->create();' . PHP_EOL
        . '$searchResults->setSearchCriteria($criteria);' . PHP_EOL
        . '$searchResults->setItems($collection->getItems());' . PHP_EOL
        . '$searchResults->setTotalCount($collection->getSize());' . PHP_EOL
        . 'return $searchResults;');
        return $namespace;
    }
}
