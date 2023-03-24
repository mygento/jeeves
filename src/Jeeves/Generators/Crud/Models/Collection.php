<?php

namespace Mygento\Jeeves\Generators\Crud\Models;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpNamespace;

class Collection extends Common
{
    public function genResourceCollection(
        string $entity,
        string $entityClass,
        string $resourceClass,
        string $rootNamespace,
        string $interface,
        string $key,
        bool $withStore = false,
        string $phpVersion = PHP_VERSION
    ) {
        $typehint = $this->hasTypes($phpVersion);
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity);
        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
            $namespace->addUse($entityClass);
            $namespace->addUse($resourceClass, $entity . 'Resource');
        }
        if ($withStore) {
            $namespace->addUse($rootNamespace . '\Api\Data\\' . $interface);
        }

        $class = $namespace->addClass('Collection');
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
        $construct = $class->addMethod('_construct')
            ->addComment('Define resource model')
            ->setVisibility('protected');

        if ($typehint) {
            $construct->setBody('$this->_init(' . PHP_EOL .
            self::TAB . $entity . '::class,' . PHP_EOL .
            self::TAB . $entity . 'Resource::class' . PHP_EOL .
            ');');
        } else {
            $construct->setBody('$this->_init(' . PHP_EOL .
            self::TAB . $entityClass . '::class,' . PHP_EOL .
            self::TAB . $resourceClass . '::class' . PHP_EOL .
            ');');
        }
        $idField = $class->addProperty('_idFieldName', $key)
            ->setVisibility('protected');
        $idField->addComment('@var string');

        if ($typehint) {
            $idField->setValue(new Literal($entity . 'Resource::TABLE_PRIMARY_KEY'));
        }

        if (!$withStore) {
            return $namespace;
        }

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\EntityManager\MetadataPool');
            $namespace->addUse('\Magento\Framework\Data\Collection\EntityFactoryInterface');
            $namespace->addUse('\Psr\Log\LoggerInterface');
            $namespace->addUse('\Magento\Framework\Data\Collection\Db\FetchStrategyInterface');
            $namespace->addUse('\Magento\Framework\Event\ManagerInterface');
            $namespace->addUse('\Psr\Log\LoggerInterface');
            $namespace->addUse('\Magento\Framework\DB\Adapter\AdapterInterface');
            $namespace->addUse('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');
        }

        if (!$constructorProp) {
            $mdPool = $class->addProperty('metadataPool');
            $mdPool->setVisibility('private');

            if ($typehint) {
                $mdPool->setType('\Magento\Framework\EntityManager\MetadataPool');
            } else {
                $mdPool->addComment('@var \Magento\Framework\EntityManager\MetadataPool');
            }
        }

        $construct = $class->addMethod('__construct')->setVisibility('public');

        if (!$typehint) {
            $construct->addComment('@param \Magento\Framework\EntityManager\MetadataPool $metadataPool')
                ->addComment('@param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory')
                ->addComment('@param \Psr\Log\LoggerInterface $logger')
                ->addComment('@param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy')
                ->addComment('@param \Magento\Framework\Event\ManagerInterface $eventManager')
                ->addComment('@param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection')
                ->addComment('@param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource');
        }
        $construct->addComment('@SuppressWarnings(PHPMD.ExcessiveParameterList)');

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('metadataPool')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Framework\EntityManager\MetadataPool');
        } else {
            $construct
                ->addParameter('metadataPool')
                ->setType('\Magento\Framework\EntityManager\MetadataPool');
        }
        $construct
            ->addParameter('entityFactory')
            ->setType('\Magento\Framework\Data\Collection\EntityFactoryInterface');
        $construct
            ->addParameter('logger')
            ->setType('\Psr\Log\LoggerInterface');
        $construct
            ->addParameter('fetchStrategy')
            ->setType('\Magento\Framework\Data\Collection\Db\FetchStrategyInterface');
        $construct
            ->addParameter('eventManager')
            ->setType('\Magento\Framework\Event\ManagerInterface');
        $construct
            ->addParameter('connection')
            ->setType('\Magento\Framework\DB\Adapter\AdapterInterface')
            ->setDefaultValue(null);
        $construct
            ->addParameter('resource')
            ->setType('\Magento\Framework\Model\ResourceModel\Db\AbstractDb')
            ->setDefaultValue(null);

        $body = 'parent::__construct(' . PHP_EOL
            . self::TAB . '$entityFactory,' . PHP_EOL
            . self::TAB . '$logger,' . PHP_EOL
            . self::TAB . '$fetchStrategy,' . PHP_EOL
            . self::TAB . '$eventManager,' . PHP_EOL
            . self::TAB . '$connection,' . PHP_EOL
            . self::TAB . '$resource' . PHP_EOL
            . ');';
        if (!$constructorProp) {
            $body .= PHP_EOL
            . '$this->metadataPool = $metadataPool;';
        }
        $construct->setBody($body);

        $afterLoad = $class->addMethod('_afterLoad')->setVisibility('protected');
        $afterLoad->setBody(
            '$entityMetadata = $this->metadataPool->getMetadata(' . $interface . '::class);' . PHP_EOL
            . '$linkedIds = $this->getColumnValues($entityMetadata->getLinkField());' . PHP_EOL . PHP_EOL
            . 'if (!count($linkedIds)) {' . PHP_EOL
            . self::TAB . 'return parent::_afterLoad();' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . '$connection = $this->getConnection();' . PHP_EOL
            . '$select = $connection->select()->from(' . PHP_EOL
            . self::TAB . '[\'entity_store\' => $this->getTable($this->getMainTable() . \'_store\')]' . PHP_EOL
            . ')->where(\'entity_store.entity_id IN (?)\', $linkedIds);' . PHP_EOL . PHP_EOL
            . '$result = $connection->fetchAll($select);' . PHP_EOL
            . 'if (!$result) {' . PHP_EOL
            . self::TAB . 'return parent::_afterLoad();' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . '$stores = [];' . PHP_EOL
            . 'foreach ($result as $r) {' . PHP_EOL
            . self::TAB . '$stores[] = $r[\'store_id\'];' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . 'foreach ($this as $item) {' . PHP_EOL
            . self::TAB . '$item->setData(\'store_id\', $stores);' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . 'return parent::_afterLoad();'
        );

        $renderFilterBefore = $class->addMethod('_renderFiltersBefore')->setVisibility('protected');
        $renderFilterBefore->setBody(
            'if (!$this->getFilter(\'store_id\')) {' . PHP_EOL
            . self::TAB . 'parent::_renderFiltersBefore();' . PHP_EOL
            . self::TAB . 'return;' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL

            . '$entityMetadata = $this->metadataPool->getMetadata(' . $interface . '::class);' . PHP_EOL
            . '$linkField = $entityMetadata->getLinkField();' . PHP_EOL . PHP_EOL

            . '$this->getSelect()->join(' . PHP_EOL
            . self::TAB . '[\'store_table\' => $this->getMainTable().\'_store\'],' . PHP_EOL
            . self::TAB . '\'main_table.\' . $linkField . \' = store_table.entity_id\',' . PHP_EOL
            . self::TAB . '[]'
            . ')->group(\'main_table.\' . $linkField);' . PHP_EOL . PHP_EOL

            . 'parent::_renderFiltersBefore();'
        );

        return $namespace;
    }
}
