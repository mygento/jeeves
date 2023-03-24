<?php

namespace Mygento\Jeeves\Generators;

use Sabre\Xml\Service;

class Common
{
    protected const TAB = '    ';
    protected const A = 'attributes';
    protected const N = 'name';
    protected const V = 'value';

    protected function hasTypes(string $version): bool
    {
        return version_compare($version, '7.4.0', '>=');
    }

    protected function hasConstructorProp(string $version): bool
    {
        return version_compare($version, '8.0.0', '>=');
    }

    protected function hasReadOnlyProp(string $version): bool
    {
        return version_compare($version, '8.1.0', '>=');
    }

    protected function hasReadOnlyClass(string $version): bool
    {
        return version_compare($version, '8.2.0', '>=');
    }

    protected function getService(): Service
    {
        $service = new Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }
}
