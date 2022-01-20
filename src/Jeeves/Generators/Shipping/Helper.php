<?php

namespace Mygento\Jeeves\Generators\Shipping;

use Mygento\Jeeves\Generators\Common;
use Nette\PhpGenerator\PhpNamespace;

class Helper extends Common
{
    public function genHelper($method, $rootNamespace): PhpNamespace
    {
        $namespace = new PhpNamespace($rootNamespace . '\Helper');
        $class = $namespace->addClass('Data');
        $class->setExtends('\Mygento\Shipment\Helper\Data');

        $class->addProperty('code', $method)
            ->setVisibility('protected')->addComment('@var string');

        return $namespace;
    }
}
