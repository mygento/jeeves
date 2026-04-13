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
        ?string $cacheTag = null,
        array $fields = self::DEFAULT_FIELDS,
        bool $hasApi = false,
        bool $withStore = false,
        string $phpVersion = PHP_VERSION,
    ): PhpNamespace {
        $hasTypedConst = $this->hasTypedConst($phpVersion);
        $namespace = new PhpNamespace($rootNamespace . '\Api\Data');
        $interface = $namespace->addInterface($className);

        if ($hasApi) {
            $interface->addComment('@api');
        }

        if ($cacheTag !== null) {
            $namespace->addUse('\Magento\Framework\DataObject\IdentityInterface');
            $interface->setExtends('\Magento\Framework\DataObject\IdentityInterface');
            $ctag = $interface->addConstant('CACHE_TAG', $cacheTag)->setVisibility('public');
            if ($hasTypedConst) {
                $ctag->setType('string');
            }
        }

        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }

        $pk = [];

        foreach ($fields as $name => $value) {
            $notNullable = $this->isNotNullable($value);
            if (isset($value['pk']) && $value['pk'] === true) {
                $pk[$name] = $value;
                $pk[$name]['nullable'] = !$notNullable;
            }
            $generated = false;
            if (isset($value['identity']) && $value['identity'] === true) {
                $generated = true;
            }
            $c = $interface->addConstant(strtoupper($name), strtolower($name))
                ->setPublic();
            if ($hasTypedConst) {
                $c->setType('string');
            }

            $getter = $this->createGetterName($name, $value);
            $get = $interface->addMethod($getter[0])
                ->setVisibility('public');
            $get->addComment($getter[1]);

            $setter = $this->createSetterName($name, $value);
            $set = $interface->addMethod($setter[0]);
            $set->addComment($setter[1])
                ->setVisibility('public');
            $param = $set->addParameter($this->snakeCaseToCamelCase($name));

            $get->setReturnType($this->convertType($value['type']));
            $get->setReturnNullable($this->shouldReturnNull($value));
            $param->setNullable(!$notNullable);
            $param->setType($this->convertType($value['type']));
            $set->setReturnType('self');

            if ($hasApi || $withStore) {
                $get->addComment(
                    '@return ' . $this->convertType($value['type']) . ($notNullable ? '' : '|null'),
                );
                $set->addComment('@return $this');
            }

            if (in_array($this->snakeCaseToCamelCase($name), ['id', 'entityId'])) {
                $param->setNullable(false);
                $param->setType(null);

                $set->addComment(
                    '@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name),
                );
            }
        }

        if ($primary !== self::DEFAULT_KEY && count($pk) === 1) {
            $item = current($pk);

            $generated = false;
            if (isset($item['identity']) && $item['identity'] === true) {
                $generated = true;
            }

            $getId = $interface
                ->addMethod('getId')
                ->addComment('Get ID')
                ->setVisibility('public');

            if ($hasApi) {
                $getId->addComment(
                    '@return ' . $this->convertType($item['type']) . ($item['nullable'] ? '|null' : ''),
                );
            }

            $getId->setReturnType($this->convertType($item['type']));
            $getId->setReturnNullable($generated ? true : $item['nullable']);

            $setId = $interface
                ->addMethod('setId')
                ->addComment('Set ID')
                ->setVisibility('public');

            $setId->addParameter(self::DEFAULT_KEY);
            $setId->addComment(
                '@param ' . $this->convertType($item['type']) . ' $id',
            );

            if ($hasApi || $withStore) {
                $setId->addComment('@return $this');
            }

            $setId->setReturnType('self');
            // $setIdParam->setType($this->convertType($item['type']));
            // $setIdParam->setNullable($item['nullable']);
        }

        return $namespace;
    }
}
