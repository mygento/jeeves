<?php

namespace Mygento\Jeeves\Generators\Crud;

class Api
{
    const VERSION = '/v1/';

    public function generateAPI($entity, $repository, $acl, $prefix)
    {
        $service = $this->getService();
        return $service->write('routes', function ($writer) use ($entity, $repository, $acl, $prefix) {
            $writer->setIndentString('    ');
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Webapi:etc/webapi.xsd'
            );
            $writer->write([
                [
                    'name' => 'route',
                    'attributes' => [
                        'url' => self::VERSION . $prefix . '/:' . $entity . 'Id',
                        'method' => 'GET'
                    ],
                    'value' => [
                        'service' => [
                            'attributes' => [
                                'class' => $repository,
                                'method' => 'getById'
                            ],
                        ],
                        'resources' => [
                            'resource' => [
                                'attributes' => [
                                    'ref' => $acl
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
            $writer->write([
                [
                    'name' => 'route',
                    'attributes' => [
                        'url' => self::VERSION . $prefix . '/search',
                        'method' => 'GET'
                    ],
                    'value' => [
                        'service' => [
                            'attributes' => [
                                'class' => $repository,
                                'method' => 'getList'
                            ],
                        ],
                        'resources' => [
                            'resource' => [
                                'attributes' => [
                                    'ref' => $acl
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
            $writer->write([
                [
                    'name' => 'route',
                    'attributes' => [
                        'url' => self::VERSION . $prefix,
                        'method' => 'POST'
                    ],
                    'value' => [
                        'service' => [
                            'attributes' => [
                                'class' => $repository,
                                'method' => 'save'
                            ],
                        ],
                        'resources' => [
                            'resource' => [
                                'attributes' => [
                                    'ref' => $acl
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
            $writer->write([
                [
                    'name' => 'route',
                    'attributes' => [
                        'url' => self::VERSION . $prefix . '/:id',
                        'method' => 'PUT'
                    ],
                    'value' => [
                        'service' => [
                            'attributes' => [
                                'class' => $repository,
                                'method' => 'save'
                            ],
                        ],
                        'resources' => [
                            'resource' => [
                                'attributes' => [
                                    'ref' => $acl
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
            $writer->write([
                [
                    'name' => 'route',
                    'attributes' => [
                        'url' => self::VERSION . $prefix . '/:' . $entity . 'Id',
                        'method' => 'DELETE'
                    ],
                    'value' => [
                        'service' => [
                            'attributes' => [
                                'class' => $repository,
                                'method' => 'deleteById'
                            ],
                        ],
                        'resources' => [
                            'resource' => [
                                'attributes' => [
                                    'ref' => $acl
                                ],
                            ]
                        ]
                    ]
                ]
            ]);
        });
    }

    private function getService()
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        return $service;
    }
}
