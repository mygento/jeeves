<?php
namespace Mygento\Jeeves\Util;

class XmlManager
{
    public function generateDI($gui, $repository, $repositoryInt, $model, $modelInt, $searchInt, $dataSource, $gridCollection, $entityTable, $eventP, $eventO, $resource)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($gui, $repository, $repositoryInt, $model, $modelInt, $searchInt, $dataSource, $gridCollection, $entityTable, $eventP, $eventO, $resource) {
            $writer->setIndentString('    ');
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:ObjectManager/etc/config.xsd');
            $writer->write([
                [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $repositoryInt,
                        'type' => $repository
                    ]
                ],
                [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $modelInt,
                        'type' => $model
                    ]
                ],
                [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $searchInt,
                        'type' => 'Magento\Framework\Api\SearchResults'
                    ]
                ],
                [
                    'name' => 'type',
                    'attributes' => [
                        'name' => 'Magento\Framework\Model\Entity\RepositoryFactory'
                    ],
                    'value' => [
                        'arguments' => [
                            [
                                'argument' => [
                                    'attributes' => [
                                        'name' => 'entities',
                                        'xsi:type' => 'array'
                                    ],
                                    'value' => [
                                        'name' => 'item',
                                        'attributes' => [
                                            'name' => $modelInt,
                                            'xsi:type' => 'string'
                                        ],
                                        'value' => $repositoryInt
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
            ]);
            if ($gui) {
                $writer->write([
                    [
                        'name' => 'type',
                        'attributes' => [
                            'name' => 'Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory'
                        ],
                        'value' => [
                            'arguments' => [
                                [
                                    'argument' => [
                                        'attributes' => [
                                            'name' => 'collections',
                                            'xsi:type' => 'array'
                                        ],
                                        'value' => [
                                            'name' => 'item',
                                            'attributes' => [
                                                'name' => $dataSource,
                                                'xsi:type' => 'string'
                                            ],
                                            'value' => $gridCollection
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        'name' => 'type',
                        'attributes' => [
                            'name' => $gridCollection
                        ],
                        'value' => [
                            'arguments' => [
                                [
                                    'name' => 'argument',
                                    'attributes' => [
                                        'name' => 'mainTable',
                                        'xsi:type' => 'string'
                                    ],
                                    'value' => $entityTable
                                ],
                                [
                                    'name' => 'argument',
                                    'attributes' => [
                                        'name' => 'eventPrefix',
                                        'xsi:type' => 'string'
                                    ],
                                    'value' => $eventP
                                ],
                                [
                                    'name' => 'argument',
                                    'attributes' => [
                                        'name' => 'eventObject',
                                        'xsi:type' => 'string'
                                    ],
                                    'value' => $eventO
                                ],
                                [
                                    'name' => 'argument',
                                    'attributes' => [
                                        'name' => 'resourceModel',
                                        'xsi:type' => 'string'
                                    ],
                                    'value' => $resource
                                ]
                            ]
                        ]
                    ]
                ]);
            }
        });
    }

    public function generateAdminRoute($module, $path, $fullname)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($module, $path, $fullname) {
            $writer->setIndentString('    ');
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:App/etc/routes.xsd');
            $writer->write([
              'name' => 'router',
              'attributes' => [
                'id' => 'admin'
              ],
              'value' => [
                  [
                    'name' => 'route',
                    'attributes' => [
                      'id' => strtolower($module),
                      'frontName' => $path,
                    ],
                    'value' => [
                      'name' => 'module',
                      'attributes' => [
                        'name' => $fullname,
                        'before' => 'Magento_Backend',
                      ],
                    ]
                  ]
              ]
            ]);
        });
    }

    public function generateAdminMenu($entity, $path, $fullname, $module)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($entity, $path, $fullname, $module) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Backend:etc/menu.xsd');
            $writer->setIndentString('    ');
            $writer->write([
                'menu' => [
                    [
                        'name'=> 'add',
                        'attributes' => [
                          'id' => $fullname . '::' . $module,
                          'title' => ucfirst($module),
                          'translate' => 'title',
                          'module' => $fullname,
                          'sortOrder' => '90',
                          'parent' => 'Magento_Backend::stores',
                          'resource' => $fullname . '::' . $module,
                        ],
                    ],
                    [
                        'name'=> 'add',
                        'attributes' => [
                          'id' => $fullname . '::' . $module . '_' . $entity,
                          'title' => ucfirst($entity),
                          'translate' => 'title',
                          'module' => $fullname,
                          'sortOrder' => '90',
                          'parent' => $fullname . '::' . $module,
                          'action' => $path . '/' . $entity,
                          'resource' => $fullname . '::' . $module . '_' . $entity,
                        ],
                    ]
                ]
            ]);
        });
    }

    public function generateAdminAcl($entity, $fullname, $module)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($entity, $fullname, $module) {
            $writer->setIndentString('    ');
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Acl/etc/acl.xsd');
            $writer->write([
                'acl' => [
                    'resources' => [
                        'resource' => [
                            'attributes' => [
                                'id' => 'Magento_Backend::admin'
                            ],
                            'value' => [
                                [
                                    'name' => 'resource',
                                    'attributes' => [
                                        'id' => $fullname . '::' . $module,
                                        'title' => ucfirst($module),
                                        'translate' => 'title'
                                    ],
                                    'value' => [
                                        [
                                            'name' => 'resource',
                                            'attributes' => [
                                                'id' => $fullname . '::' . $module . '_' . $entity,
                                                'title' => ucfirst($module) . ' ' . ucfirst($entity),
                                                'translate' => 'title'
                                            ],
                                        ],
                                    ]
                                ],
                                [
                                    'name' => 'resource',
                                    'attributes' => [
                                        'id' => 'Magento_Backend::stores',
                                    ],
                                    'value' => [
                                        'resource' => [
                                            'attributes' => [
                                                'id' => 'Magento_Backend::stores_settings',
                                            ],
                                            'value' => [
                                                'resource' => [
                                                    'attributes' => [
                                                        'id' => 'Magento_Config::config',
                                                    ],
                                                    'value' => [
                                                        'resource' => [
                                                            'attributes' => [
                                                                'id' => $fullname . '::config',
                                                                'title' => str_replace('_', ' ', $fullname),
                                                                'translate' => 'title'
                                                            ],
                                                        ],
                                                    ]
                                                ],
                                            ]
                                        ],
                                    ]
                                ]
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
