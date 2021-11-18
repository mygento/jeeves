<?php

namespace Mygento\Jeeves\Generators\Crud\Layouts;

use Mygento\Jeeves\Generators\Crud\Common;

class Edit extends Common
{
    public function generateAdminLayoutEdit(string $uiComponent): string
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
                    'update' => [
                        'attributes' => [
                            'handle' => 'editor',
                        ],
                    ],
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
