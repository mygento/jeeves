<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;

class Dependency extends Common
{
    public function generateDI(array $di): string
    {
        $service = $this->getService();

        return $service->write('config', function ($writer) use ($di) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:ObjectManager/etc/config.xsd');
            $writer->write($di);
        });
    }
}
