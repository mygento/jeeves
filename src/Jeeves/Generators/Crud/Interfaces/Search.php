<?php

namespace Mygento\Jeeves\Generators\Crud\Interfaces;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Search extends Common
{
    public function genModelSearchInterface(
        string $entity,
        string $className,
        string $print,
        string $entInterface,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Api\Data');
        $namespace->addUse('\Magento\Framework\Api\SearchResultsInterface');

        $interface = $namespace->addInterface($className);
        $interface->setExtends('\Magento\Framework\Api\SearchResultsInterface');

        $get = $interface->addMethod('getItems')
            ->setVisibility('public')
            ->addComment('Get list of ' . $print);
        if ($typehint) {
            // $get->setReturnType('array');
        }
        $get->addComment('@return ' . $entInterface . '[]');

        $set = $interface->addMethod('setItems')
            ->setVisibility('public')
            ->addComment('Set list of ' . $print)
            ->addComment('@param ' . $entInterface . '[] $items');
        $set->addParameter('items')->setTypeHint('array');

        if ($typehint) {
            // $set->setReturnType('self');
        }

        return $namespace;
    }
}
