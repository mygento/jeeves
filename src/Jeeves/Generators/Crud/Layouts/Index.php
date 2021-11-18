<?php

namespace Mygento\Jeeves\Generators\Crud\Layouts;

use Mygento\Jeeves\Generators\Crud\Common;

class Index extends Common
{
    public function generateAdminLayoutIndex(string $uiComponent): string
    {
        $service = $this->getService();

        return $service->write('page', function ($writer) use ($uiComponent) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:framework:View/Layout/etc/page_configuration.xsd'
            );
            $writer->write([
                [
                    'body' => [
                        'referenceContainer' => [
                            'attributes' => [
                                'name' => 'content',
                            ],
                            'value' => [
                                [
                                    'uiComponent' => [
                                        'attributes' => [
                                            'name' => $uiComponent,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }
}
