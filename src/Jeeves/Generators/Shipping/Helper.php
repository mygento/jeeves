<?php

namespace Mygento\Jeeves\Generators\Shipping;

use Nette\PhpGenerator\PhpNamespace;

class Helper
{
    public function genHelper($method, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Helper');
        $class = $namespace->addClass('Data');
        $class->setExtends('\Mygento\Shipment\Helper\Data');

        $class->addProperty('code', $method)
            ->setVisibility('protected')->addComment('@var string');
        return $namespace;
    }
}
