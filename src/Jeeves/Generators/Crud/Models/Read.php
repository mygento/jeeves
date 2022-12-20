<?php

namespace Mygento\Jeeves\Generators\Crud\Models;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Read extends Common
{
    public function genReadHandler(
        string $entity,
        string $resourceClass,
        string $interface,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = version_compare($phpVersion, '7.4.0', '>=');
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity . '\\Relation\\Store');
        $namespace->addUse($interface);

        $class = $namespace->addClass('ReadHandler');
        $class->addImplement('\Magento\Framework\EntityManager\Operation\ExtensionInterface');

        $res = $class->addProperty('resource')->setVisibility('private');
        if ($typehint) {
            $namespace->addUse('\Magento\Framework\EntityManager\Operation\ExtensionInterface');
            $namespace->addUse($resourceClass);
            $res->setType($resourceClass);
        } else {
            $res->addComment('@var ' . $resourceClass);
        }

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
            . self::TAB . '$entity->setData(' . $namespace->simplifyType($interface) . '::STORE_ID, $stores);' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . 'return $entity;');
        $execute->addParameter('entity');
        $execute->addParameter('arguments')->setDefaultValue([]);

        return $namespace;
    }
}
