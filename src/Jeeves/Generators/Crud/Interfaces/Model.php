<?php

namespace Mygento\Jeeves\Generators\Crud\Interfaces;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Model extends Common
{
    public function genModelInterface(
        string $className,
        string $primary,
        string $rootNamespace,
        string $cacheTag = null,
        array $fields = self::DEFAULT_FIELDS,
        bool $withStore = false,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Api\Data');
        $interface = $namespace->addInterface($className);

        if ($cacheTag !== null) {
            $namespace->addUse('\Magento\Framework\DataObject\IdentityInterface');
            $interface->setExtends('\Magento\Framework\DataObject\IdentityInterface');
            $interface->addConstant('CACHE_TAG', $cacheTag)->setVisibility('public');
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
            $interface->addConstant(strtoupper($name), strtolower($name))->setPublic();
            $method = $this->snakeCaseToUpperCamelCase($name);
            $get = $interface->addMethod('get' . $method)
                ->setVisibility('public');
            $get->addComment('Get ' . str_replace('_', ' ', $name));

            $set = $interface->addMethod('set' . $method);
            $set->addComment('Set ' . str_replace('_', ' ', $name))
                ->setVisibility('public');
            $param = $set->addParameter($this->snakeCaseToCamelCase($name));

            if ($typehint) {
                $get->setReturnType($this->convertType($value['type']));
                $get->setReturnNullable(!$notNullable);
                $param->setNullable(!$notNullable);
                $param->setType($this->convertType($value['type']));
                $set->setReturnType('self');
            } else {
                $get->addComment('@return ' . $this->convertType($value['type']) . ($notNullable ? '' : '|null'));
                $set->addComment('@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name))
                    ->addComment('@return $this');
            }

            if ($this->snakeCaseToCamelCase($name) == 'id') {
                $param->setNullable(false);
                $param->setType(null);

                if ($typehint) {
                    $set->addComment('@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name));
                }
            }
        }

        if ($primary !== self::DEFAULT_KEY && count($pk) === 1) {
            $item = current($pk);

            $getId = $interface
                ->addMethod('getId')
                ->addComment('Get ID')
                ->setVisibility('public');

            if ($typehint) {
                $getId->setReturnType($this->convertType($item['type']));
                $getId->setReturnNullable($item['nullable']);
            }

            $setId = $interface
                ->addMethod('setId')
                ->addComment('Set ID')
                ->setVisibility('public');

            $setIdParam = $setId->addParameter(self::DEFAULT_KEY);
            $setId->addComment('@param ' . $this->convertType($item['type']) . ' $id');

            if ($typehint) {
                $setId->setReturnType('self');
                // $setIdParam->setType($this->convertType($item['type']));
                // $setIdParam->setNullable($item['nullable']);
            }
        }

        return $namespace;
    }
}
