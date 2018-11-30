<?php
namespace Mygento\Jeeves\Util;

class XmlManager
{
    public function generateDI($guiList, $entities, $namespace, $module)
    {
        $service = $this->getService();
        $repositoriesList = array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $namespace . '\\Api\\' . ucfirst($entity) . 'RepositoryInterface',
                        'type' => $namespace . '\\Model\\' . ucfirst($entity) . 'Repository'
                    ]
                ];
            },
            $entities
        );
        $modelList = array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'type' => $namespace . '\\Model\\' . ucfirst($entity),
                    ]
                ];
            },
            $entities
        );
        $searchList = array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'SearchResultsInterface',
                        'type' => 'Magento\Framework\Api\SearchResults'
                    ]
                ];
            },
            $entities
        );
        $repoFactoryList  = array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'xsi:type' => 'string'
                    ],
                    'value' => $namespace . '\\Api\\' . ucfirst($entity) . 'RepositoryInterface',
                ];
            },
            $entities
        );
        $gridList  = array_map(
            function ($entity) use ($namespace, $module) {
                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $module . '_' . $entity . '_listing_data_source',
                        'xsi:type' => 'string'
                    ],
                    'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Grid\\Collection',
                ];
            },
            array_keys($guiList)
        );
        $gridCollections  = array_map(
            function ($entity, $tablename) use ($namespace, $module) {
                return [
                    'name' => 'type',
                    'attributes' => [
                        'name' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Grid\\Collection',
                    ],
                    'value' => [
                        'arguments' => [
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'mainTable',
                                    'xsi:type' => 'string'
                                ],
                                'value' => $tablename
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'eventPrefix',
                                    'xsi:type' => 'string'
                                ],
                                'value' => $module . '_' . $entity . '_grid_collection',
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'eventObject',
                                    'xsi:type' => 'string'
                                ],
                                'value' => $entity . '_grid_collection',
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'resourceModel',
                                    'xsi:type' => 'string'
                                ],
                                'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity)
                            ]
                        ]
                    ]
                ];
            },
            array_keys($guiList),
            $guiList
        );
        return $service->write('config', function ($writer) use ($guiList, $repositoriesList, $modelList, $searchList, $repoFactoryList, $gridList, $gridCollections) {
            $writer->setIndentString('    ');
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:ObjectManager/etc/config.xsd');
            $writer->write(array_merge(
                $repositoriesList,
                $modelList,
                $searchList,
                [
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
                                        'value' => $repoFactoryList,
                                    ]
                                ]
                            ]
                        ]
                    ],
                ]
            ));
            if (empty($guiList)) {
                return;
            }
            $writer->write([
                array_merge(
                    [
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
                                            'value' => $gridList
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ],
                    $gridCollections
                )
            ]);
        });
    }

    public function generateAdminRoute($path, $fullname, $module)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($path, $fullname, $module) {
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

    public function generateAdminMenu($entities, $fullname, $module)
    {
        $entityList = array_map(
            function ($entity, $path) use ($fullname, $module) {
                return [
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
                ];
            },
            array_keys($entities),
            $entities
        );
        $service = $this->getService();
        $common = [
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
        ];
        return $service->write('config', function ($writer) use ($common, $entityList) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Backend:etc/menu.xsd');
            $writer->setIndentString('    ');
            $writer->write([
                'menu' => array_merge([$common], $entityList),
            ]);
        });
    }

    public function generateAdminAcl($entities, $fullname, $module)
    {
        $service = $this->getService();
        $entityList = array_map(
            function ($entity) use ($fullname, $module) {
                return [
                    'name' => 'resource',
                    'attributes' => [
                        'id' => $fullname . '::' . $module . '_' . $entity,
                        'title' => ucfirst($module) . ' ' . ucfirst($entity),
                        'translate' => 'title'
                    ],
                ];
            },
            $entities
        );
        return $service->write('config', function ($writer) use ($entityList, $fullname, $module) {
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
                                    'value' => $entityList,
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
