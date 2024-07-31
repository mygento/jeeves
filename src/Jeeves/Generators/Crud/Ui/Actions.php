<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Actions extends Common
{
    public function getActions(
        string $route,
        string $controller,
        string $className,
        string $primaryKey,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Ui\Component\Listing');
        $class = $namespace->addClass($className);
        $class->setExtends('\Mygento\Base\Ui\Component\Listing\Actions');
        $router = $class->addProperty('route', $route)
            ->setVisibility('protected');
        $cont = $class->addProperty('controller', $controller)
            ->setVisibility('protected');

        $namespace->addUse('\Mygento\Base\Ui\Component\Listing\Actions');
        $router->setType('string');
        $cont->setType('string');

        if ($primaryKey !== 'id') {
            $key = $class->addProperty('key', $primaryKey)
                ->setVisibility('protected');

            $key->setType('string');
        }

        return $namespace;
    }
}
