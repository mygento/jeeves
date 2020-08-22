<?php

namespace Mygento\Jeeves\Util\Xml;

class Di
{
    public function getRepositories($entities, $namespace)
    {
        return array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $namespace . '\\Api\\' . ucfirst($entity) . 'RepositoryInterface',
                        'type' => $namespace . '\\Model\\' . ucfirst($entity) . 'Repository',
                    ],
                ];
            },
            array_keys($entities)
        );
    }

    public function getModels($entities, $namespace)
    {
        return array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'type' => $namespace . '\\Model\\' . ucfirst($entity),
                    ],
                ];
            },
            array_keys($entities)
        );
    }

    public function getSearch($entities, $namespace)
    {
        return array_map(
            function ($entity) use ($namespace) {
                return [
                    'name' => 'preference',
                    'attributes' => [
                        'for' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'SearchResultsInterface',
                        'type' => 'Magento\Framework\Api\SearchResults',
                    ],
                ];
            },
            array_keys($entities)
        );
    }

    public function getRepoFactory($entities, $namespace)
    {
        return array_map(
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
            array_keys($entities)
        );
    }

    public function getGrid($module, $namespace, $guiList)
    {
        return array_map(
            function ($entity) use ($namespace, $module) {
                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $this->getConverter()->getLowerCaseModuleEntity($module, $entity) . '_listing_data_source',
                        'xsi:type' => 'string',
                    ],
                    'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Grid\\Collection',
                ];
            },
            array_keys($guiList)
        );
    }

    public function getGridCollections($module, $namespace, $guiList)
    {
        return array_map(
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
                                'value' => $this->getConverter()->getLowerCaseModuleEntity($module, $entity) . '_grid_collection',
                            ],
                            [
                                'name' => 'argument',
                                'attributes' => [
                                    'name' => 'eventObject',
                                    'xsi:type' => 'string',
                                ],
                                'value' => $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($entity) . '_grid_collection',
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
    }

    public function getEntityManager($entities, $namespace)
    {
        return array_filter(array_map(
            function ($entity, $value) use ($namespace) {
                if (!$value['store']) {
                    return [];
                }

                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'xsi:type' => 'array',
                    ],
                    'value' => [
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'entityTableName',
                                'xsi:type' => 'string',
                            ],
                            'value' => $value['table'],
                        ],
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'identifierField',
                                'xsi:type' => 'string',
                            ],
                            'value' => $value['id'],
                        ],
                    ],
                ];
            },
            array_keys($entities),
            $entities
        ));
    }

    public function getEntityExtension($entities, $namespace)
    {
        return array_filter(array_map(
            function ($entity, $value) use ($namespace) {
                if (!$value['store']) {
                    return [];
                }

                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'xsi:type' => 'array',
                    ],
                    'value' => [
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'read',
                                'xsi:type' => 'array',
                            ],
                            'value' => [
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => 'storeReader',
                                        'xsi:type' => 'string',
                                    ],
                                    'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Relation\Store\ReadHandler',
                                ],
                            ],
                        ],
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'create',
                                'xsi:type' => 'array',
                            ],
                            'value' => [
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => 'storeCreator',
                                        'xsi:type' => 'string',
                                    ],
                                    'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Relation\Store\SaveHandler',
                                ],
                            ],
                        ],
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'update',
                                'xsi:type' => 'array',
                            ],
                            'value' => [
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => 'storeUpdater',
                                        'xsi:type' => 'string',
                                    ],
                                    'value' => $namespace . '\\Model\\ResourceModel\\' . ucfirst($entity) . '\\Relation\Store\SaveHandler',
                                ],
                            ],
                        ],
                    ],
                ];
            },
            array_keys($entities),
            $entities
        ));
    }

    public function getEntityHydrator($entities, $namespace)
    {
        return array_filter(array_map(
            function ($entity, $value) use ($namespace) {
                if (!$value['store']) {
                    return [];
                }

                return [
                    'name' => 'item',
                    'attributes' => [
                        'name' => $namespace . '\\Api\\Data\\' . ucfirst($entity) . 'Interface',
                        'xsi:type' => 'string',
                    ],
                    'value' => 'Magento\Framework\EntityManager\AbstractModelHydrator',
                ];
            },
            array_keys($entities),
            $entities
        ));
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
