<?php

namespace Mygento\Jeeves\Generators\Crud\Layouts;

use Mygento\Jeeves\Generators\Crud\Common;

class Create extends Common
{
    public function generateAdminLayoutNew(string $handle): string
    {
        $service = $this->getService();

        return $service->write('page', function ($writer) use ($handle) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:framework:View/Layout/etc/page_configuration.xsd'
            );
            $writer->write([
                [
                    'update' => [
                        'attributes' => [
                            'handle' => $handle,
                        ],
                    ],
                ],
            ]);
        });
    }
}
