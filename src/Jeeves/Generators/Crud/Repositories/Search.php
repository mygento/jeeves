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
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse($interface);
        $namespace->addUse('\Magento\Framework\Api\SearchResults');

        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Framework\Api\SearchResults');
        $class->setImplements([$interface]);

        return $namespace;
    }
}
