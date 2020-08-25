<?php

namespace Mygento\Jeeves\Util;

class XmlManager
{
    const A = 'attributes';
    const N = 'name';
    const V = 'value';

    private $shipping;

    private $di;

    private $db;

    public function generateShippingSystem($module, $entity, $namespace)
    {
        $service = $this->getService();

        return $service->write('config', function ($writer) use ($module, $entity, $namespace) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Config:etc/system_file.xsd');
            $writer->setIndentString('    ');
            $writer->write([
                'system' => [
                    'section' => [
                        self::A => [
                            'id' => 'carriers',
                        ],
                        self::V => [
                            'group' => [
                                self::A => [
                                    'id' => $module,
                                    'translate' => 'label',
                                    'type' => 'text',
                                    'sortOrder' => '99',
                                    'showInDefault' => '1',
                                    'showInWebsite' => '1',
                                    'showInStore' => '1',
                                ],
                                self::V => [
                                    'label' => $entity,
                                    $this->getShipping()->getEnabled(),
                                    $this->getShipping()->getTitle(),
                                    $this->getShipping()->getSort(),
                                    $this->getShipping()->getTest(),
                                    $this->getShipping()->getDebug(),
                                    $this->getShipping()->getAuthGroup(),
                                    $this->getShipping()->getOptionsGroup(),
                                    $this->getShipping()->getPackageGroup($module),
                                    $this->getShipping()->getTaxGroup($namespace),
                                    $this->getShipping()->getOrderStatusGroup(),
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    public function generateDI($guiList, $entities, $namespace, $module)
    {
        $service = $this->getService();
        $repositoriesList = $this->getDi()->getRepositories($entities, $namespace);
        $repoProcessor = $this->getDi()->getRepoProcessors($entities, $namespace);
        $repoProcessorList = $this->getDi()->getRepoProcessorList($entities, $namespace);
        $filterProcessors = $this->getDi()->getFilterProcessors($entities, $namespace);
        $modelList = $this->getDi()->getModels($entities, $namespace);
        $searchList = $this->getDi()->getSearch($entities, $namespace);
        $repoFactoryList = $this->getDi()->getRepoFactory($entities, $namespace);
        $gridList = $this->getDi()->getGrid($module, $namespace, $guiList);
        $gridCollections = $this->getDi()->getGridCollections($module, $namespace, $guiList);
        $entityManager = $this->getDi()->getEntityManager($entities, $namespace);
        $entityExt = $this->getDi()->getEntityExtension($entities, $namespace);
        $entityHydrator = $this->getDi()->getEntityHydrator($entities, $namespace);

        return $service->write('config', function ($writer) use (
            $guiList,
            $repositoriesList,
            $modelList,
            $searchList,
            $repoFactoryList,
            $gridList,
            $gridCollections,
            $entityManager,
            $entityExt,
            $entityHydrator,
            $repoProcessor,
            $repoProcessorList,
            $filterProcessors
        ) {
            $writer->setIndentString('    ');
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:ObjectManager/etc/config.xsd');
            $writer->write(array_merge(
                $repositoriesList,
                $modelList,
                $searchList,
                [
                    [
                        self::N => 'type',
                        self::A => [
                            'name' => 'Magento\Framework\Model\Entity\RepositoryFactory',
                        ],
                        self::V => [
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
                ],
                $filterProcessors,
                $repoProcessorList,
                $repoProcessor
            ));
            if (!empty($entityManager)) {
                $writer->write([
                    self::N => 'type',
                    self::A => [
                        'name' => 'Magento\Framework\EntityManager\MetadataPool',
                    ],
                    self::V => [
                        'arguments' => [
                            'argument' => [
                                'attributes' => [
                                    'name' => 'metadata',
                                    'xsi:type' => 'array',
                                ],
                                'value' => $entityManager,
                            ],
                        ],
                    ],
                ]);
            }
            if (!empty($entityExt)) {
                $writer->write([
                    self::N => 'type',
                    self::A => [
                        'name' => 'Magento\Framework\EntityManager\Operation\ExtensionPool',
                    ],
                    self::V => [
                        'arguments' => [
                            'argument' => [
                                'attributes' => [
                                    'name' => 'extensionActions',
                                    'xsi:type' => 'array',
                                ],
                                'value' => $entityExt,
                            ],
                        ],
                    ],
                ]);
            }
            if (!empty($entityHydrator)) {
                $writer->write([
                    self::N => 'type',
                    self::A => [
                        'name' => 'Magento\Framework\EntityManager\HydratorPool',
                    ],
                    self::V => [
                        'arguments' => [
                            'argument' => [
                                'attributes' => [
                                    'name' => 'hydrators',
                                    'xsi:type' => 'array',
                                ],
                                'value' => $entityHydrator,
                            ],
                        ],
                    ],
                ]);
            }
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
                        'id' => $fullname . '::' . $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($entity),
                        'title' => $this->getConverter()->splitAtUpperCase($entity),
                        'translate' => 'title',
                        'module' => $fullname,
                        'sortOrder' => '90',
                        'parent' => $fullname . '::root',
                        'action' => $path . '/' . $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($entity),
                        'resource' => $fullname . '::' . $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($entity),
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
                          . $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($entity),
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
                $primaryContraintList = $this->getDb()->getPrimary($entity);
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
                $constraintList = $this->getDb()->getConstraint($entity, $tablename);
                $indexList = $this->getDb()->getIndexes($entity, $tablename);
                $indexFKList = $this->getDb()->getIndexFK($entity);

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

    public function generateModule($fullname)
    {
        $service = $this->getService();

        return $service->write('config', function ($writer) use ($fullname) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Module/etc/module.xsd');
            $writer->setIndentString('    ');
            $writer->write([
                'module' => [
                    'attributes' => [
                        'name' => $fullname,
                    ],
                ],
            ]);
        });
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
                ];
                break;
            case 'price':
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
        $columnType = $param['type'];
        if ($param['type'] === 'price') {
            $columnType = 'decimal';
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
                'xsi:type' => $columnType,
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

    private function getShipping()
    {
        if (!$this->shipping) {
            $this->shipping = new Xml\Shipping();
        }

        return $this->shipping;
    }

    private function getDi(): Xml\Di
    {
        if (!$this->di) {
            $this->di = new Xml\Di();
        }

        return $this->di;
    }

    private function getDb()
    {
        if (!$this->db) {
            $this->db = new Xml\DbSchema();
        }

        return $this->db;
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
