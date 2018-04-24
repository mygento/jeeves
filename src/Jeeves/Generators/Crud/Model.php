<?php

namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class Model
{
    public function genModel($className, $entInterface, $resource, $rootNamespace)
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
        return $namespace;
    }

    public function genResourceModel($className, $table, $key, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel');
        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');

        $class->addMethod('_construct')
            ->addComment('Initialize resource model')
            ->addComment('@return void')
            ->setVisibility('protected')
            ->setBody('$this->_init(\'' . $table . '\', \'' . $key . '\');');
        return $namespace;
    }

    public function genResourceCollection($entity, $entityClass, $resourceClass, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\ResourceModel\\' . $entity);
        $class = $namespace->addClass('Collection');
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
        $class->addMethod('_construct')
            ->addComment('Define resource model')
            ->setVisibility('protected')
            ->setBody('$this->_init(' . PHP_EOL .
            '   ' . $entityClass . '::class,' . PHP_EOL .
            '   ' . $resourceClass . '::class' . PHP_EOL .
            ');');
        return $namespace;
    }
}
