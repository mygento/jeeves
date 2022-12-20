<?php

namespace Mygento\Jeeves\Generators\Crud\Models;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Save extends Common
{
    public function genSaveHandler(
        string $entity,
        string $interface,
        string $resourceClass,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = version_compare($phpVersion, '7.4.0', '>=');
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity . '\\Relation\\Store');
        $namespace->addUse($interface);
        $namespace->addUse($rootNamespace . '\Model\ResourceModel\\' . $entity);
        $namespace->addUse('\Magento\Framework\EntityManager\MetadataPool');

        $class = $namespace->addClass('SaveHandler');
        $class->addImplement('\Magento\Framework\EntityManager\Operation\ExtensionInterface');

        $res = $class->addProperty('resource')
            ->setVisibility('private');
        $mtdPool = $class->addProperty('metadataPool')
            ->setVisibility('private');

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\EntityManager\Operation\ExtensionInterface');
            $res->setType($resourceClass);
            $mtdPool->setType('\Magento\Framework\EntityManager\MetadataPool');
        } else {
            $res->addComment('@var ' . $resourceClass);
            $mtdPool->addComment('@var \Magento\Framework\EntityManager\MetadataPool');
        }

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
                '$entityMetadata = $this->metadataPool->getMetadata(' . $namespace->simplifyType($interface) . '::class);' . PHP_EOL
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
                . '}' . PHP_EOL . PHP_EOL
                . 'return $entity;'
            );
        $execute->addParameter('entity');
        $execute->addParameter('arguments')->setDefaultValue([]);

        return $namespace;
    }
}
