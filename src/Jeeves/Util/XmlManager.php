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
                        'type' => $namespace . '\\Model\\' . ucfirst($entity) . 'Repository',
                    ],
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
                    ],
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
                        'type' => 'Magento\Framework\Api\SearchResults',
                    ],
                ];
            },
            $entities
        );
        $repoFactoryList = array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'xsi:type' => 'string',
                    ],
                    'value' => $namespace . '\\Api\\' . ucfirst($entity) . 'RepositoryInterface',
                ];
            },
            $entities
        );
        $gridList = array_map(
            function ($entity) use ($namespace, $module) {
                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $this->getConverter()->camelCaseToSnakeCase($module) . '_' . $this->getConverter()->camelCaseToSnakeCase($entity) . '_listing_data_source',
                        'xsi:type' => 'string',
                    ],
                    'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Grid\\Collection',
                ];
            },
            array_keys($guiList)
        );
        $gridCollections = array_map(
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
                                    'xsi:type' => 'string',
                                ],
                                'value' => $tablename,
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'eventPrefix',
                                    'xsi:type' => 'string',
                                ],
                                'value' => $this->getConverter()->camelCaseToSnakeCase($module) . '_' . $this->getConverter()->camelCaseToSnakeCase($entity) . '_grid_collection',
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'eventObject',
                                    'xsi:type' => 'string',
                                ],
                                'value' => $this->getConverter()->camelCaseToSnakeCase($entity) . '_grid_collection',
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'resourceModel',
                                    'xsi:type' => 'string',
                                ],
                                'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity),
                            ],
                        ],
                    ],
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
                            'name' => 'Magento\Framework\Model\Entity\RepositoryFactory',
                        ],
                        'value' => [
                            'arguments' => [
                                [
                                    'argument' => [
                                        'attributes' => [
                                            'name' => 'entities',
                                            'xsi:type' => 'array',
                                        ],
                                        'value' => $repoFactoryList,
                                    ],
                                ],
                            ],
                        ],
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
                                'name' => 'Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory',
                            ],
                            'value' => [
                                'arguments' => [
                                    [
                                        'argument' => [
                                            'attributes' => [
                                                'name' => 'collections',
                                                'xsi:type' => 'array',
                                            ],
                                            'value' => $gridList,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    $gridCollections
                ),
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
                    'id' => 'admin',
                ],
                'value' => [
                    [
                        'name' => 'route',
                        'attributes' => [
                            'id' => $this->getConverter()->camelCaseToSnakeCase($module),
                            'frontName' => $path,
                        ],
                        'value' => [
                            'name' => 'module',
                            'attributes' => [
                                'name' => $fullname,
                                'before' => 'Magento_Backend',
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    public function generateAdminMenu($entities, $fullname, $module)
    {
        $entityList = array_map(
            function ($entity, $path) use ($fullname, $module) {
                return [
                    'name' => 'add',
                    'attributes' => [
                        'id' => $fullname . '::' . $this->getConverter()->camelCaseToSnakeCase($entity),
                        'title' => $this->getConverter()->splitAtUpperCase($entity),
                        'translate' => 'title',
                        'module' => $fullname,
                        'sortOrder' => '90',
                        'parent' => $fullname . '::root',
                        'action' => $path . '/' . $this->getConverter()->camelCaseToSnakeCase($entity),
                        'resource' => $fullname . '::' . $this->getConverter()->camelCaseToSnakeCase($entity),
                    ],
                ];
            },
            array_keys($entities),
            $entities
        );
        $service = $this->getService();
        $common = [
            'name' => 'add',
            'attributes' => [
                'id' => $fullname . '::root',
                'title' => $this->getConverter()->splitAtUpperCase($module),
                'translate' => 'title',
                'module' => $fullname,
                'sortOrder' => '90',
                'parent' => 'Magento_Backend::stores',
                'resource' => $fullname . '::root',
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
                        'id' => $fullname . '::'
                          . $this->getConverter()->camelCaseToSnakeCase($entity),
                        'title' => $this->getConverter()->splitAtUpperCase($module)
                            . ' ' . $this->getConverter()->splitAtUpperCase($entity),
                        'translate' => 'title',
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
                                'id' => 'Magento_Backend::admin',
                            ],
                            'value' => [
                                [
                                    'name' => 'resource',
                                    'attributes' => [
                                        'id' => $fullname . '::root',
                                        'title' => $this->getConverter()->splitAtUpperCase($module),
                                        'translate' => 'title',
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
                                                                'title' => $this->getConverter()->getEntityName($fullname),
                                                                'translate' => 'title',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                            ],
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

    public function generateSchema($db)
    {
        $service = $this->getService();
        $entityList = array_map(
            function ($tablename, $entity) {
                $entity['indexes'] = $entity['indexes'] ?? [];
                $entity['fk'] = $entity['fk'] ?? [];
                $columnList = array_map(
                    [$this, 'getColumn'],
                    array_keys($entity['columns']),
                    $entity['columns']
                );
                $primaryContraintList = array_map(
                    function ($column, $param) {
                        if (!isset($param['pk']) && !isset($param['identity'])) {
                            return [];
                        }
                        if (isset($param['pk']) && false == $param['pk']) {
                            return [];
                        }
                        if (isset($param['identity']) && false == $param['identity']) {
                            return [];
                        }

                        return [
                            'name' => 'column',
                            'attributes' => [
                                'name' => $column,
                            ],
                        ];
                    },
                    array_keys($entity['columns']),
                    $entity['columns']
                );
                $primaryContraint = [];
                if (!empty($primaryContraintList)) {
                    $primaryContraint = [
                        'name' => 'constraint',
                        'attributes' => [
                            'xsi:type' => 'primary',
                            'referenceId' => 'PRIMARY',
                        ],
                        'value' => $primaryContraintList,
                    ];
                }
                $constraintList = array_map(
                    function ($name, $param) use ($tablename) {
                        return [
                            'name' => 'constraint',
                            'attributes' => [
                                'xsi:type' => 'foreign',
                                'referenceId' => $name,
                                'table' => $tablename,
                                'column' => $param['column'],
                                'referenceTable' => $param['referenceTable'],
                                'referenceColumn' => $param['referenceColumn'],
                                'onDelete' => $param['onDelete'] ?? 'CASCADE',
                            ],
                        ];
                    },
                    array_keys($entity['fk']),
                    $entity['fk']
                );
                $indexList = array_map(
                    function ($name, $param) {
                        $param['type'] = $param['type'] ?? 'btree';
                        if ('unique' === $param['type']) {
                            return [
                                'name' => 'constraint',
                                'attributes' => [
                                    'xsi:type' => 'unique',
                                    'referenceId' => $name,
                                ],
                                'value' => array_map(
                                    [$this, 'getIndexColumn'],
                                    $param['columns']
                                ),
                            ];
                        }

                        return [
                            'name' => 'index',
                            'attributes' => [
                                'referenceId' => $name,
                                'indexType' => $param['type'],
                            ],
                            'value' => array_map(
                                [$this, 'getIndexColumn'],
                                $param['columns']
                            ),
                        ];
                    },
                    array_keys($entity['indexes']),
                    $entity['indexes']
                );
                $indexColumn = array_filter(array_map(
                    function ($indx) {
                        if (count($indx['columns']) > 1) {
                            return null;
                        }

                        return $indx['columns'][0];
                    },
                    array_values($entity['indexes'])
                ));
                $indexFKList = array_filter(array_map(
                    function ($column, $param) use ($indexColumn) {
                        if (in_array($param['column'], $indexColumn)) {
                            return [];
                        }

                        return [
                            'name' => 'index',
                            'attributes' => [
                                'indexType' => 'btree',
                                'referenceId' => $param['indexName'],
                            ],
                            'value' => array_map(
                                [$this, 'getIndexColumn'],
                                [$param['column']]
                            ),
                        ];
                    },
                    array_keys($entity['fk']),
                    $entity['fk']
                ));

                return [
                    'name' => 'table',
                    'attributes' => [
                        'name' => $tablename,
                        'resource' => 'default',
                        'engine' => 'innodb',
                        'comment' => $entity['comment'] ?? $tablename . ' Table',
                    ],
                    'value' => array_merge(
                        $columnList,
                        [$primaryContraint],
                        $indexList,
                        $constraintList,
                        $indexFKList
                    ),
                ];
            },
            array_keys($db),
            $db
        );

        return $service->write('schema', function ($writer) use ($entityList) {
            $writer->setIndentString('    ');
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd');
            $writer->write($entityList);
        });
    }

    private function getIndexColumn($name)
    {
        return [
            'name' => 'column',
            'attributes' => [
                'name' => $name,
            ],
        ];
    }

    private function getColumn($column, $param)
    {
        $optional = [];
        switch ($param['type']) {
            case 'blob':
            case 'boolean':
            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'varbinary':
                $type = [];
                break;
            case 'int':
            case 'smallint':
            case 'bigint':
            case 'tinyint':
                $type = [
                    'identity' => var_export($param['identity'] ?? false, true), //autoinrement
                    'unsigned' => var_export($param['unsigned'] ?? false, true),
                    'padding' => var_export($param['padding'] ?? 10, true),
                ];
                break;
            case 'real':
            case 'decimal':
            case 'float':
            case 'double':
                $type = [
                    'precision' => var_export($param['precision'] ?? 10, true),
                    'scale' => var_export($param['scale'] ?? 4, true),
                ];
                break;
            case 'text':
            case 'mediumtext':
            case 'longtext':
                $type = [];
                break;
            case 'varchar':
                $type = [
                    'length' => var_export($param['length'] ?? 255, true),
                ];
                break;
            default:
                throw new \Exception('Error column type');
                break;
        }
        if (isset($param['default'])) {
            $optional['default'] = (string) $param['default'];
        }
        if (isset($param['on_update'])) {
            $optional['on_update'] = var_export($param['on_update'], true);
        }

        return [
            'name' => 'column',
            'attributes' => array_merge([
                'xsi:type' => $param['type'],
                'name' => $column,
                'nullable' => var_export($param['nullable'] ?? false, true),
            ], $type, $optional, ['comment' => $param['comment'] ?? ucfirst($column)]),
        ];
    }

    private function getService()
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }

    /**
     * Get Converter.
     *
     * @return \Mygento\Jeeves\Generators\Crud\Common
     */
    private function getConverter()
    {
        return new \Mygento\Jeeves\Generators\Crud\Common();
    }
}
