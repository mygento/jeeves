<?php

namespace Mygento\Jeeves\Generators\Crud\Models;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Resource extends Common
{
    public function genResourceModel(
        string $className,
        string $table,
        string $key,
        string $rootNamespace,
        string $interface,
        bool $withStore = false,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel');

        if ($typehint) {
            $namespace->addUse('Magento\Framework\Model\ResourceModel\Db\AbstractDb');
        }

        if ($withStore) {
            $namespace->addUse('Magento\Framework\Model\AbstractModel');
            $namespace->addUse($rootNamespace . '\Api\Data\\' . $interface);
        }

        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');

        $class->addConstant('TABLE_NAME', $table)->setVisibility('public');
        $class->addConstant('TABLE_PRIMARY_KEY', $key)->setVisibility('public');

        $class->addMethod('_construct')
            ->addComment('Initialize resource model')
            ->setVisibility('protected')
            ->setBody('$this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);');

        if (!$withStore) {
            return $namespace;
        }

        $em = $class->addProperty('entityManager');
        $em->setVisibility('private');

        $mdPool = $class->addProperty('metadataPool');
        $mdPool->setVisibility('private');

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\EntityManager\EntityManager');
            $em->setType('\Magento\Framework\EntityManager\EntityManager');
            $namespace->addUse('\Magento\Framework\EntityManager\MetadataPool');
            $mdPool->setType('\Magento\Framework\EntityManager\MetadataPool');
            $namespace->addUse('\Magento\Framework\Model\ResourceModel\Db\Context');
        } else {
            $em->addComment('@var \Magento\Framework\EntityManager\EntityManager');
            $mdPool->addComment('@var \Magento\Framework\EntityManager\MetadataPool');
        }

        $construct = $class->addMethod('__construct');
        $construct->setVisibility('public');
        if (!$typehint) {
            $construct
                ->addComment('@param \Magento\Framework\EntityManager\EntityManager $entityManager')
                ->addComment('@param \Magento\Framework\EntityManager\MetadataPool $metadataPool')
                ->addComment('@param \Magento\Framework\Model\ResourceModel\Db\Context $context')
                ->addComment('@param string $connectionName');
        }

        $construct->addParameter('entityManager')->setType('\Magento\Framework\EntityManager\EntityManager');
        $construct->addParameter('metadataPool')->setType('\Magento\Framework\EntityManager\MetadataPool');
        $construct->addParameter('context')->setType('\Magento\Framework\Model\ResourceModel\Db\Context');
        $conParam = $construct->addParameter('connectionName')->setDefaultValue(null);

        if ($typehint) {
            $conParam->setType('string');
        }

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

        $lookup = $class->addMethod('lookupStoreIds');
        $lookup->addComment('Find store ids to which specified item is assigned')
            ->addComment('')

            ->setVisibility('public');

        $lookIdParam = $lookup->addParameter('id');
        if ($typehint) {
            $lookup->setReturnType('array');
            $lookIdParam->setType('int');
        } else {
            $lookup
                ->addComment('@param int $id')
                ->addComment('@return array');
        }
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
}