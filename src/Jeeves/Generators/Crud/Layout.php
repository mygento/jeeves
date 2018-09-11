<?php

namespace Mygento\Jeeves\Generators\Crud;

class Layout
{
    private function getService()
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        return $service;
    }

    public function generateAdminLayoutIndex($uiComponent)
    {
        $service = $this->getService();
        $xml = $service->write('page', function ($writer) use ($uiComponent) {
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
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            ]);
        });
        return $xml;
    }

    public function generateAdminLayoutEdit($uiComponent)
    {
        $service = $this->getService();
        $xml = $service->write('page', function ($writer) use ($uiComponent) {
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
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            ]);
        });
        return $xml;
    }

    public function generateAdminLayoutNew($uiComponent, $handle)
    {
        $service = $this->getService();
        $xml = $service->write('page', function ($writer) use ($uiComponent) {
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:framework:View/Layout/etc/page_configuration.xsd'
            );
            $writer->write([
                [
                    'update' => [
                        'attributes' => [
                          'handle' => 'content',
                        ],
                    ],
                ]
            ]);
        });
        return $xml;
    }
}
