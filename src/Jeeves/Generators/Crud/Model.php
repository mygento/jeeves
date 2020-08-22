<?php

namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class Model extends Common
{
    public function genModel($className, $entInterface, $resource, $rootNamespace, $fields = self::DEFAULT_FIELDS, $withStore = false)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('Magento\Framework\Model\AbstractModel');
        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Framework\Model\AbstractModel');
        $class->setImplements([$entInterface]);

        $class->addMethod('_construct')
            ->addComment('@return void')
            ->setVisibility('protected')
            ->setBody('$this->_init(' . $resource . '::class);');

        $class->addMethod('getIdentities')
            ->addComment('@return string[]')
            ->setVisibility('public')
            ->setBody('return [self::CACHE_TAG . \'_\' . $this->getId()];');

        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }

        foreach ($fields as $name => $value) {
            $method = $this->snakeCaseToUpperCamelCase($name);
            $class->addMethod('get' . $method)
                ->addComment('Get ' . str_replace('_', ' ', $name))
                ->addComment('@return ' . $this->convertType($value['type']) . '|null')
                ->setVisibility('public')->setBody('return $this->getData(self::' . strtoupper($name) . ');');
            $setter = $class->addMethod('set' . $method)
                ->addComment('Set ' . str_replace('_', ' ', $name))
                ->addComment('@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name))
                ->addComment('@return $this')
                ->setVisibility('public');
            $setter->addParameter($this->snakeCaseToCamelCase($name));
            $setter->setBody('return $this->setData(self::' . strtoupper($name) . ', $' . $this->snakeCaseToCamelCase($name) . ');');
        }

        return $namespace;
    }

    public function genResourceModel($className, $table, $key, $rootNamespace, $interface, $withStore = false)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel');
        if ($withStore) {
            $namespace->addUse('Magento\Framework\Model\AbstractModel');
            $namespace->addUse($rootNamespace . '\Api\Data\\' . $interface);
        }

        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');

        $class->addMethod('_construct')
            ->addComment('Initialize resource model')
            ->addComment('@return void')
            ->setVisibility('protected')
            ->setBody('$this->_init(\'' . $table . '\', \'' . $key . '\');');

        if (!$withStore) {
            return $namespace;
        }

        $class->addProperty('entityManager')
            ->setVisibility('private')->addComment('@var \Magento\Framework\EntityManager\EntityManager');
        $class->addProperty('metadataPool')
            ->setVisibility('private')->addComment('@var \Magento\Framework\EntityManager\MetadataPool');

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\EntityManager\EntityManager $entityManager')
            ->addComment('@param \Magento\Framework\EntityManager\MetadataPool $metadataPool')
            ->addComment('@param \Magento\Framework\Model\ResourceModel\Db\Context $context')
            ->addComment('@param string $connectionName')
            ->setVisibility('public');

        $construct->addParameter('entityManager')->setType('\Magento\Framework\EntityManager\EntityManager');
        $construct->addParameter('metadataPool')->setType('\Magento\Framework\EntityManager\MetadataPool');
        $construct->addParameter('context')->setType('\Magento\Framework\Model\ResourceModel\Db\Context');
        $construct->addParameter('connectionName')->setDefaultValue(null);

        $construct->setBody('parent::__construct($context, $connectionName);' . PHP_EOL
            . '$this->entityManager = $entityManager;' . PHP_EOL
            . '$this->metadataPool = $metadataPool;');

        $connection = $class->addMethod('getConnection')
            ->addComment('@inheritDoc')
            ->setVisibility('public');
        $connection->setBody('return $this->metadataPool->getMetadata(' . $interface . '::class)->getEntityConnection();');

        $load = $class->addMethod('load')
            ->addComment('@inheritDoc')
            ->addComment('')
            ->addComment('@SuppressWarnings(PHPMD.UnusedFormalParameter)')
            ->setVisibility('public');

        $load->addParameter('object')->setType('\Magento\Framework\Model\AbstractModel');
        $load->addParameter('value');
        $load->addParameter('field')->setDefaultValue(null);
        $load->setBody('return $this->entityManager->load($object, $value);');

        $save = $class->addMethod('save')
            ->addComment('@inheritDoc')
            ->setVisibility('public');

        $save->addParameter('object')->setType('\Magento\Framework\Model\AbstractModel');
        $save->setBody('$this->entityManager->save($object);' . PHP_EOL . 'return $this;');

        $delete = $class->addMethod('delete')
            ->addComment('@inheritDoc')
            ->setVisibility('public');

        $delete->addParameter('object')->setType('\Magento\Framework\Model\AbstractModel');
        $delete->setBody('$this->entityManager->delete($object);' . PHP_EOL . 'return $this;');

        $lookup = $class->addMethod('lookupStoreIds')
            ->addComment('Find store ids to which specified item is assigned')
            ->addComment('')
            ->addComment('@param int $id')
            ->addComment('@return array')
            ->setVisibility('public');

        $lookup->addParameter('id');
        $lookup->setBody(
            '$connection = $this->getConnection();' . PHP_EOL . PHP_EOL

            . '$entityMetadata = $this->metadataPool->getMetadata(' . $interface . '::class);' . PHP_EOL
            . '$linkField = $entityMetadata->getLinkField();' . PHP_EOL . PHP_EOL

            . '$select = $connection->select()' . PHP_EOL
            . self::TAB . '->from([\'es\' => $this->getMainTable().\'_store\'], \'store_id\')' . PHP_EOL
            . self::TAB . '->join(' . PHP_EOL
            . self::TAB . self::TAB . '[\'e\' => $this->getMainTable()],' . PHP_EOL
            . self::TAB . self::TAB . '\'es.entity_id = e.\'.$linkField,' . PHP_EOL
            . self::TAB . self::TAB . '[]' . PHP_EOL
            . self::TAB . ')' . PHP_EOL
            . self::TAB . '->where(\'e.\' . $entityMetadata->getIdentifierField() . \' = :entity_id\');' . PHP_EOL . PHP_EOL

            . 'return $connection->fetchCol($select, [\'entity_id\' => (int) $id]);'
        );

        return $namespace;
    }

    public function genResourceCollection($entity, $entityClass, $resourceClass, $rootNamespace, $interface, $withStore = false)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity);
        if ($withStore) {
            $namespace->addUse($rootNamespace . '\Api\Data\\' . $interface);
        }
        $class = $namespace->addClass('Collection');
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
        $class->addMethod('_construct')
            ->addComment('Define resource model')
            ->setVisibility('protected')
            ->setBody('$this->_init(' . PHP_EOL .
            self::TAB . $entityClass . '::class,' . PHP_EOL .
            self::TAB . $resourceClass . '::class' . PHP_EOL .
            ');');
        $class->addProperty('_idFieldName', 'id')
            ->setVisibility('protected')
            ->addComment('@var string');
        if (!$withStore) {
            return $namespace;
        }

        $class->addProperty('metadataPool')
            ->setVisibility('private')->addComment('@var \Magento\Framework\EntityManager\MetadataPool');

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\EntityManager\MetadataPool $metadataPool')
            ->addComment('@param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory')
            ->addComment('@param \Psr\Log\LoggerInterface $logger')
            ->addComment('@param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy')
            ->addComment('@param \Magento\Framework\Event\ManagerInterface $eventManager')
            ->addComment('@param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection')
            ->addComment('@param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource')
            ->addComment('@SuppressWarnings(PHPMD.ExcessiveParameterList)')
            ->setVisibility('public');

        $construct->addParameter('metadataPool')->setType('\Magento\Framework\EntityManager\MetadataPool');
        $construct->addParameter('entityFactory')->setType('\Magento\Framework\Data\Collection\EntityFactoryInterface');
        $construct->addParameter('logger')->setType('\Psr\Log\LoggerInterface');
        $construct->addParameter('fetchStrategy')->setType('\Magento\Framework\Data\Collection\Db\FetchStrategyInterface');
        $construct->addParameter('eventManager')->setType('\Magento\Framework\Event\ManagerInterface');
        $construct->addParameter('connection')->setType('\Magento\Framework\DB\Adapter\AdapterInterface')->setDefaultValue(null);
        $construct->addParameter('resource')->setTypeHint('\Magento\Framework\Model\ResourceModel\Db\AbstractDb')->setDefaultValue(null);

        $construct->setBody('parent::__construct(' . PHP_EOL
            . self::TAB . '$entityFactory,' . PHP_EOL
            . self::TAB . '$logger,' . PHP_EOL
            . self::TAB . '$fetchStrategy,' . PHP_EOL
            . self::TAB . '$eventManager,' . PHP_EOL
            . self::TAB . '$connection,' . PHP_EOL
            . self::TAB . '$resource' . PHP_EOL
            . ');' . PHP_EOL
            . '$this->metadataPool = $metadataPool;');

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
            . 'if (!$result) {'
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

    public function genReadHandler($entity, $resourceClass, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity . '\\Relation\\Store');
        $class = $namespace->addClass('ReadHandler');
        $class->addImplement('\Magento\Framework\EntityManager\Operation\ExtensionInterface');

        $class->addProperty('resource')
            ->setVisibility('private')->addComment('@var ' . $resourceClass);

        $construct = $class->addMethod('__construct')
            ->setVisibility('public');
        $construct->addParameter('resource')->setType($resourceClass);
        $construct->setBody('$this->resource = $resource;');

        $execute = $class->addMethod('execute')
            ->addComment('@inheritDoc')
            ->addComment('@SuppressWarnings(PHPMD.UnusedFormalParameter)')
            ->setVisibility('public')
            ->setBody('if ($entity->getId()) {' . PHP_EOL
            . self::TAB . '$stores = $this->resource->lookupStoreIds((int) $entity->getId());' . PHP_EOL
            . self::TAB . '$entity->setData(\'store_id\', $stores);' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $entity;');
        $execute->addParameter('entity');
        $execute->addParameter('arguments')->setDefaultValue([]);

        return $namespace;
    }

    public function genSaveHandler($entity, $interface, $resourceClass, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity . '\\Relation\\Store');
        $namespace->addUse($rootNamespace . '\Api\Data\\' . $interface);
        $namespace->addUse($rootNamespace . '\Model\ResourceModel\\' . $entity);
        $namespace->addUse('\Magento\Framework\EntityManager\MetadataPool');
        $class = $namespace->addClass('SaveHandler');
        $class->addImplement('\Magento\Framework\EntityManager\Operation\ExtensionInterface');

        $class->addProperty('resource')
            ->setVisibility('private')->addComment('@var ' . $resourceClass);
        $class->addProperty('metadataPool')
            ->setVisibility('private')->addComment('@var \Magento\Framework\EntityManager\MetadataPool');

        $construct = $class->addMethod('__construct')
            ->setVisibility('public');
        $construct->addParameter('resource')->setType($resourceClass);
        $construct->addParameter('metadataPool')->setType('\Magento\Framework\EntityManager\MetadataPool');
        $construct->setBody('$this->resource = $resource;' . PHP_EOL . '$this->metadataPool = $metadataPool;');

        $execute = $class->addMethod('execute')
            ->addComment('@inheritDoc')
            ->addComment('@SuppressWarnings(PHPMD.UnusedFormalParameter)')
            ->setVisibility('public')
            ->setBody(
                '$entityMetadata = $this->metadataPool->getMetadata(' . $interface . '::class);' . PHP_EOL
                . '$linkField = $entityMetadata->getLinkField();' . PHP_EOL
                . '$connection = $entityMetadata->getEntityConnection();' . PHP_EOL
                . '$oldStores = $this->resource->lookupStoreIds((int) $entity->getId());' . PHP_EOL
                . '$newStores = (array) $entity->getStoreId();' . PHP_EOL
                . '$table = $this->resource->getTable($entityMetadata->getEntityTable() . \'_store\');' . PHP_EOL . PHP_EOL
                . '$delete = array_diff($oldStores, $newStores);' . PHP_EOL
                . 'if ($delete) {' . PHP_EOL
                . self::TAB . '$where = [' . PHP_EOL
                . self::TAB . self::TAB . '\'entity_id = ?\' => (int) $entity->getData($linkField),' . PHP_EOL
                . self::TAB . self::TAB . '\'store_id IN (?)\' => $delete,' . PHP_EOL
                . self::TAB . '];' . PHP_EOL
                . self::TAB . '$connection->delete($table, $where);' . PHP_EOL
                . '}' . PHP_EOL . PHP_EOL
                . '$insert = array_diff($newStores, $oldStores);' . PHP_EOL
                . 'if ($insert) {' . PHP_EOL
                . self::TAB . '$data = [];' . PHP_EOL
                . self::TAB . 'foreach ($insert as $storeId) {' . PHP_EOL
                . self::TAB . self::TAB . '$data[] = [' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . '\'entity_id\' => (int) $entity->getData($linkField),' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . '\'store_id\' => (int) $storeId,' . PHP_EOL
                . self::TAB . self::TAB . '];' . PHP_EOL
                . self::TAB . '}' . PHP_EOL
                . self::TAB . '$connection->insertMultiple($table, $data);' . PHP_EOL
                . '}' . PHP_EOL
                . 'return $entity;'
            );
        $execute->addParameter('entity');
        $execute->addParameter('arguments')->setDefaultValue([]);

        return $namespace;
    }
}
