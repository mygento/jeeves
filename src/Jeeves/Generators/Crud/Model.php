<?php

namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class Model extends Common
{
    public function genModel($className, $entInterface, $resource, $rootNamespace, $fields = self::DEFAULT_FIELDS)
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
        $class->addProperty('_idFieldName', 'id')
            ->setVisibility('protected')
            ->addComment('@var string');

        return $namespace;
    }
}
