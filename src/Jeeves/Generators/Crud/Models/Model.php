<?php

namespace Mygento\Jeeves\Generators\Crud\Models;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Model extends Common
{
    public function genModel(
        string $className,
        string $entInterface,
        string $resource,
        string $rootNamespace,
        string $event,
        string $cacheTag = null,
        array $fields = self::DEFAULT_FIELDS,
        bool $withStore = false,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('Magento\Framework\Model\AbstractModel');
        $namespace->addUse($entInterface);

        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Framework\Model\AbstractModel');
        $class->setImplements([$entInterface]);

        $class->addProperty('_eventPrefix')
            ->setVisibility('protected')
            ->setValue($event)
            ->addComment('@inheritDoc');

        $class->addMethod('_construct')
            ->addComment('@return void')
            ->setVisibility('protected')
            ->setBody('$this->_init(' . $resource . '::class);');

        if ($cacheTag !== null) {
            $getCache = $class->addMethod('getIdentities');
            $getCache->setVisibility('public')
                ->setBody('return [self::CACHE_TAG . \'_\' . $this->getId()];');
            if ($typehint) {
                $getCache->setReturnType('array');
            } else {
                $getCache->addComment('@return string[]');
            }
        }

        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }

        $pk = [];

        foreach ($fields as $name => $value) {
            $notNullable = $this->isNullable($value);
            if (isset($value['pk']) && $value['pk'] === true) {
                $pk[$name] = $value;
                $pk[$name]['nullable'] = !$notNullable;
            }
            $method = $this->snakeCaseToUpperCamelCase($name);
            $getter = $class->addMethod('get' . $method)
                ->addComment('Get ' . str_replace('_', ' ', $name))
                ->setVisibility('public')
                ->setBody('return $this->getData(self::' . strtoupper($name) . ');');

            $setter = $class->addMethod('set' . $method)
                ->addComment('Set ' . str_replace('_', ' ', $name))
                ->setVisibility('public');
            $setParam = $setter->addParameter($this->snakeCaseToCamelCase($name));
            $setter->setBody('return $this->setData(self::' . strtoupper($name) . ', $' . $this->snakeCaseToCamelCase($name) . ');');

            if ($typehint) {
                $getter->setReturnType($this->convertType($value['type']));
                $getter->setReturnNullable(!$notNullable);

                $setter->setReturnType('self');
                $setParam->setType($this->convertType($value['type']));
                $setParam->setNullable(!$notNullable);
            } else {
                $getter->addComment('@return ' . $this->convertType($value['type']) . ($notNullable ? '' : '|null'));
                $setter
                    ->addComment('@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name))
                    ->addComment('@return $this');
            }

            if ($this->snakeCaseToCamelCase($name) == 'id') {
                $setParam->setNullable(false);
                $setParam->setType(null);

                if ($typehint) {
                    $setter->addComment('@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name));
                }
            }
        }

        if (count($pk) === 1 && !in_array('id', array_keys($pk))) {
            $item = current($pk);
            $itemName = current(array_keys($pk));

            $getId = $class
                ->addMethod('getId')
                ->addComment('Get ID')
                ->setVisibility('public')
                ->setBody('return $this->getData(self::' . strtoupper($itemName) . ');');
            if ($typehint) {
                $getId->setReturnType($this->convertType($item['type']));
                $getId->setReturnNullable($item['nullable']);
            }

            $setId = $class
                ->addMethod('setId')
                ->addComment('Set ID')
                ->setVisibility('public')
                ->setBody('return $this->setData(self::' . strtoupper($itemName) . ', $id);');
            $setIdParam = $setId->addParameter('id');
            $setId->addComment('@param ' . $this->convertType($item['type']) . ' $id');

            if ($typehint) {
                $setId->setReturnType('self');

                //$setIdParam->setType($this->convertType($item['type']));
                //$setIdParam->setNullable($item['nullable']);
            }
        }

        return $namespace;
    }
}
