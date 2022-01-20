<?php

namespace Mygento\Jeeves\Generators;

use Sabre\Xml\Service;

class Common
{
    protected const TAB = '    ';
    protected const A = 'attributes';
    protected const N = 'name';
    protected const V = 'value';

    protected function getService(): Service
    {
        $service = new Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }
}
