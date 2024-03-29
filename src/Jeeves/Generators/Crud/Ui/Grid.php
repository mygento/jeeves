<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;

class Grid extends Common
{
    public function generateGridCollection(
        string $entity,
        string $className,
        string $collection,
        string $rootNamespace,
        bool $withStore = false,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Model\\ResourceModel\\' . ucfirst($entity) . '\\Grid');
        $namespace->addUse('Magento\Framework\Api\Search\SearchResultInterface');
        $namespace->addUse('Magento\Framework\Api\SearchCriteriaInterface');
        $namespace->addUse($collection, 'ParentCollection');

        $class = $namespace->addClass($className);
        $class->setExtends($collection);
        $class->addImplement('Magento\Framework\Api\Search\SearchResultInterface');
        $agg = $class->addProperty('aggregations')
            ->setVisibility('protected'); //private?

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Api\Search\AggregationInterface');
            $agg->setType('\Magento\Framework\Api\Search\AggregationInterface');
        } else {
            $agg->addComment('@var \Magento\Framework\Api\Search\AggregationInterface');
        }

        $construct = $class->addMethod('__construct');
        if ($withStore && !$typehint) {
            $construct->addComment('@param \Magento\Framework\EntityManager\MetadataPool $metadataPool');
        }
        $construct
            ->setVisibility('public');

        if ($withStore) {
            $construct->addParameter('metadataPool')->setType('\Magento\Framework\EntityManager\MetadataPool');
        }
        $construct->addParameter('entityFactory')->setType('\Magento\Framework\Data\Collection\EntityFactoryInterface');
        $construct->addParameter('logger')->setType('\Psr\Log\LoggerInterface');
        $construct->addParameter('fetchStrategy')->setType('\Magento\Framework\Data\Collection\Db\FetchStrategyInterface');
        $construct->addParameter('eventManager')->setType('\Magento\Framework\Event\ManagerInterface');
        $construct->addParameter('mainTable')->setType('string');
        $construct->addParameter('eventPrefix')->setType('string');
        $construct->addParameter('eventObject')->setType('string');
        $construct->addParameter('resourceModel')->setType('string');
        $construct->addParameter('model')
            ->setType('string')
            ->setDefaultValue(new Literal('\Magento\Framework\View\Element\UiComponent\DataProvider\Document::class'));
        $construct->addParameter('connection')->setType('\Magento\Framework\DB\Adapter\AdapterInterface')->setDefaultValue(null);
        $construct->addParameter('resource')->setType('\Magento\Framework\Model\ResourceModel\Db\AbstractDb')->setDefaultValue(null);

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Data\Collection\EntityFactoryInterface');
            $namespace->addUse('\Psr\Log\LoggerInterface');
            $namespace->addUse('\Magento\Framework\Data\Collection\Db\FetchStrategyInterface');
            $namespace->addUse('\Magento\Framework\Event\ManagerInterface');
            $namespace->addUse('\Magento\Framework\DB\Adapter\AdapterInterface');
            $namespace->addUse('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');
            if ($withStore) {
                $namespace->addUse('\Magento\Framework\EntityManager\MetadataPool');
            }
        } else {
            $construct
                ->addComment('@param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory')
                ->addComment('@param \Psr\Log\LoggerInterface $logger')
                ->addComment('@param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy')
                ->addComment('@param \Magento\Framework\Event\ManagerInterface $eventManager')
                ->addComment('@param string $mainTable')
                ->addComment('@param string $eventPrefix')
                ->addComment('@param string $eventObject')
                ->addComment('@param string $resourceModel')
                ->addComment('@param string $model')
                ->addComment('@param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection')
                ->addComment('@param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource');
        }

        $construct->addComment('@SuppressWarnings(PHPMD.ExcessiveParameterList)');

        $construct->setBody('parent::__construct(' . PHP_EOL
            . ($withStore ? '$metadataPool,' . PHP_EOL : '')
            . '    $entityFactory,' . PHP_EOL
            . '    $logger,' . PHP_EOL
            . '    $fetchStrategy,' . PHP_EOL
            . '    $eventManager,' . PHP_EOL
            . '    $connection,' . PHP_EOL
            . '    $resource' . PHP_EOL
            . ');' . PHP_EOL
            . '$this->_eventPrefix = $eventPrefix;' . PHP_EOL
            . '$this->_eventObject = $eventObject;' . PHP_EOL
            . '$this->_init($model, $resourceModel);' . PHP_EOL
            . '$this->setMainTable($mainTable);');

        $getAggregations = $class->addMethod('getAggregations')
            ->addComment('@return \Magento\Framework\Api\Search\AggregationInterface')
            ->setVisibility('public');
        $getAggregations->setBody('return $this->aggregations;');

        $setAggregations = $class->addMethod('setAggregations')
            ->addComment('@param \Magento\Framework\Api\Search\AggregationInterface $aggregations')
            ->addComment('@return $this')
            ->setVisibility('public');
        $setAggregations->addParameter('aggregations');
        $setAggregations->setBody('$this->aggregations = $aggregations;' . PHP_EOL . 'return $this;');

        $getSearchCriteria = $class->addMethod('getSearchCriteria')
            ->addComment('@return \Magento\Framework\Api\SearchCriteriaInterface|null')
            ->setVisibility('public');
        $getSearchCriteria->setBody('return null;');

        $setSearchCriteria = $class->addMethod('setSearchCriteria')
            ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria')
            ->addComment('@return $this')
            ->addComment('@SuppressWarnings(PHPMD.UnusedFormalParameter)')
            ->setVisibility('public');
        $setSearchCriteria->addParameter('searchCriteria')
            ->setType('\Magento\Framework\Api\SearchCriteriaInterface')
            ->setDefaultValue(null);
        $setSearchCriteria->setBody('return $this;');

        $getTotalCount = $class->addMethod('getTotalCount')
            ->addComment('@return int')
            ->setVisibility('public');
        $getTotalCount->setBody('return $this->getSize();');

        $setTotalCount = $class->addMethod('setTotalCount')
            ->addComment('@param int $totalCount')
            ->addComment('@return $this')
            ->addComment('@SuppressWarnings(PHPMD.UnusedFormalParameter)')
            ->setVisibility('public');
        $setTotalCount->addParameter('totalCount');
        $setTotalCount->setBody('return $this;');

        $setItems = $class->addMethod('setItems')
            ->addComment('@param \Magento\Framework\Api\ExtensibleDataInterface[] $items')
            ->addComment('@return $this')
            ->addComment('@SuppressWarnings(PHPMD.UnusedFormalParameter)')
            ->setVisibility('public');
        $setItems->addParameter('items')->setType('array')->setDefaultValue(null);
        $setItems->setBody('return $this;');

        return $namespace;
    }
}
