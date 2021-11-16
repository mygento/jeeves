<?php

namespace Mygento\Jeeves\Generators\Crud\Repositories;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Search extends Common
{
    public function genModelSearch(
        string $className,
        string $interface,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse($interface);
        $namespace->addUse('\Magento\Framework\Api\SearchResults');

        $class = $namespace->addClass($className);
        $class->addExtend('\Magento\Framework\Api\SearchResults');
        $class->setImplements([$interface]);

        return $namespace;
    }
}
