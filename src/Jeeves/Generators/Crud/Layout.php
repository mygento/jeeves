<?php

namespace Mygento\Jeeves\Generators\Crud;

class Layout
{
    public function generateAdminLayoutIndex($uiComponent)
    {
        $service = $this->getService();
        $xml = $service->write('page', function ($writer) use ($uiComponent) {
            $writer->setIndentString('    ');
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

        return $xml;
    }

    public function generateAdminLayoutEdit($uiComponent)
    {
        $service = $this->getService();
        $xml = $service->write('page', function ($writer) use ($uiComponent) {
            $writer->setIndentString('    ');
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

        return $xml;
    }

    public function generateAdminLayoutNew($uiComponent, $handle)
    {
        $service = $this->getService();
        $xml = $service->write('page', function ($writer) use ($uiComponent, $handle) {
            $writer->setIndentString('    ');
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

        return $xml;
    }

    private function getService()
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }
}
