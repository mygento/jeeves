<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;

class Module extends Common
{
    public function generateModule(string $fullname): string
    {
        $service = $this->getService();

        return $service->write('config', function ($writer) use ($fullname) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Module/etc/module.xsd');
            $writer->setIndentString(self::TAB);
            $writer->write([
                'module' => [
                    'attributes' => [
                        'name' => $fullname,
                    ],
                ],
            ]);
        });
    }
}
