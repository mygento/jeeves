<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Model\Generator;

class Dependency extends Generator
{
    public function generate(array $entities): array
    {
        $di = [];
        $di[] = $this->getRepositories($entities);
        $di[] = $this->getModels($entities);
        $di[] = $this->getSearch($entities);
        $di[] = $this->getRepoFactory($entities);
        $di[] = $this->getFilterProcessors($entities);
        $di[] = $this->getRepoProcessorList($entities);
        $di[] = $this->getRepoProcessors($entities);
        $di[] = $this->getEntityManager($entities);
        $di[] = $this->getEntityExtension($entities);
        $di[] = $this->getEntityHydrator($entities);

        $di[] = $this->getGrid($entities);
        $di[] = $this->getGridCollections($entities);

        return $di;
    }

    private function getRepositories(array $entities): array
    {
        return array_map(
            function (Entity $entity) {
                return [
                    self::N => 'preference',
                    self::A => [
                        'for' => $entity->getNamespace() . '\\Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                        'type' => $entity->getNamespace() . '\\Model\\' . $entity->getEntityName() . 'Repository',
                    ],
                ];
            },
            $entities
        );
    }

    private function getModels(array $entities): array
    {
        return array_map(
            function (Entity $entity) {
                return [
                    self::N => 'preference',
                    self::A => [
                        'for' => $entity->getNamespace() . '\\Api\\Data\\' . $entity->getEntityName() . 'Interface',
                        'type' => $entity->getNamespace() . '\\Model\\' . $entity->getEntityName(),
                    ],
                ];
            },
            $entities
        );
    }

    private function getSearch(array $entities): array
    {
        return array_map(
            function (Entity $entity) {
                return [
                    self::N => 'preference',
                    self::A => [
                        'for' => $entity->getNamespace() . '\\Api\\Data\\' . $entity->getEntityName() . 'SearchResultsInterface',
                        'type' => $entity->getNamespace() . '\\Model\\' . $entity->getEntityName() . 'SearchResults',
                    ],
                ];
            },
            $entities
        );
    }

    private function getRepoFactory(array $entities): array
    {
        $items = array_map(
            function (Entity $entity) {
                return [
                    self::N => 'item',
                    self::A => [
                        self::N => $entity->getNamespace() . '\\Api\\Data\\' . $entity->getEntityName() . 'Interface',
                        'xsi:type' => 'string',
                    ],
                    self::V => $entity->getNamespace() . '\\Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                ];
            },
            $entities
        );

        return [
            [
                self::N => 'type',
                self::A => [
                    self::N => 'Magento\Framework\Model\Entity\RepositoryFactory',
                ],
                self::V => [
                    'arguments' => [
                        [
                            'argument' => [
                                self::A => [
                                    self::N => 'entities',
                                    'xsi:type' => 'array',
                                ],
                                self::V => $items,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getFilterProcessors(array $entities): array
    {
        return array_filter(array_map(
            function (Entity $entity) {
                if (!$entity->withStore()) {
                    return [];
                }

                $name = $entity->getNamespace() . '\\Model\SearchCriteria\\' . $entity->getEntityName() . 'FilterProcessor';
                $filter = $entity->getNamespace() . '\\Model\SearchCriteria\\' . $entity->getEntityName() . 'StoreFilter';

                return [
                    self::N => 'virtualType',
                    self::A => [
                        self::N => $name,
                        'type' => 'Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor',
                    ],
                    self::V => [
                        'arguments' => [
                            'argument' => [
                                self::A => [
                                    self::N => 'customFilters',
                                    'xsi:type' => 'array',
                                ],
                                self::V => [
                                    'item' => [
                                        self::A => [
                                            self::N => 'store_id',
                                            'xsi:type' => 'object',
                                        ],
                                        self::V => $filter,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            },
            $entities
        ));
    }

    private function getRepoProcessorList(array $entities): array
    {
        $processors = [
            'filter' => 'Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor',
            'sort' => 'Magento\Framework\Api\SearchCriteria\CollectionProcessor\SortingProcessor',
            'page' => 'Magento\Framework\Api\SearchCriteria\CollectionProcessor\PaginationProcessor',
        ];

        return array_map(
            function (Entity $entity) use ($processors) {
                $name = $entity->getNamespace() . '\\Model\\SearchCriteria\\' . $entity->getEntityName() . 'CollectionProcessor';
                $filter = $entity->getNamespace() . '\\Model\SearchCriteria\\' . $entity->getEntityName() . 'FilterProcessor';

                return [
                    self::N => 'virtualType',
                    self::A => [
                        self::N => $name,
                        'type' => 'Magento\Framework\Api\SearchCriteria\CollectionProcessor',
                    ],
                    self::V => [
                        'arguments' => [
                            'argument' => [
                                self::A => [
                                    self::N => 'processors',
                                    'xsi:type' => 'array',
                                ],
                                self::V => [
                                    [
                                        self::N => 'item',
                                        self::A => [
                                            self::N => 'filters',
                                            'xsi:type' => 'object',
                                        ],
                                        self::V => $entity->withStore() ? $filter : $processors['filter'],
                                    ],
                                    [
                                        self::N => 'item',
                                        self::A => [
                                            self::N => 'sorting',
                                            'xsi:type' => 'object',
                                        ],
                                        self::V => $processors['sort'],
                                    ],
                                    [
                                        self::N => 'item',
                                        self::A => [
                                            self::N => 'pagination',
                                            'xsi:type' => 'object',
                                        ],
                                        self::V => $processors['page'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            },
            $entities
        );
    }

    private function getRepoProcessors(array $entities): array
    {
        return array_map(
            function (Entity $entity) {
                return [
                    self::N => 'type',
                    self::A => [
                        self::N => $entity->getNamespace() . '\\Model\\' . $entity->getEntityName() . 'Repository',
                    ],
                    self::V => [
                        'arguments' => [
                            'argument' => [
                                self::A => [
                                    self::N => 'collectionProcessor',
                                    'xsi:type' => 'object',
                                ],
                                self::V => $entity->getNamespace() . '\\Model\\SearchCriteria\\' . $entity->getEntityName() . 'CollectionProcessor',
                            ],
                        ],
                    ],
                ];
            },
            $entities
        );
    }

    private function getGrid(array $entities): array
    {
        $result = array_filter(array_map(
            function (Entity $entity) {
                if (!$entity->hasGui()) {
                    return [];
                }

                return [
                    self::N => 'item',
                    self::A => [
                        self::N => $entity->getLowerCaseWithModule() . '_listing_data_source',
                        'xsi:type' => 'string',
                    ],
                    self::V => $entity->getNamespace() . '\\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Grid\\Collection',
                ];
            },
            $entities
        ));

        if (empty($result)) {
            return [];
        }

        return [
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
                                'value' => $result,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getGridCollections(array $entities): array
    {
        return array_filter(array_map(
            function (Entity $entity) {
                if (!$entity->hasGui()) {
                    return [];
                }

                return [
                    self::N => 'type',
                    self::A => [
                        self::N => $entity->getNamespace() . '\\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Grid\\Collection',
                    ],
                    self::V => [
                        'arguments' => [
                            [
                                self::N => 'argument',
                                self::A => [
                                    self::N => 'mainTable',
                                    'xsi:type' => 'string',
                                ],
                                self::V => $entity->getTablename(),
                            ],
                            [
                                self::N => 'argument',
                                self::A => [
                                    self::N => 'eventPrefix',
                                    'xsi:type' => 'string',
                                ],
                                self::V => $entity->getLowerCaseWithModule() . '_grid_collection',
                            ],
                            [
                                self::N => 'argument',
                                self::A => [
                                    self::N => 'eventObject',
                                    'xsi:type' => 'string',
                                ],
                                self::V => $entity->getEventObject() . '_grid_collection',
                            ],
                            [
                                self::N => 'argument',
                                self::A => [
                                    self::N => 'resourceModel',
                                    'xsi:type' => 'string',
                                ],
                                self::V => $entity->getNamespace() . '\\Model\\ResourceModel\\' . $entity->getEntityName(),
                            ],
                        ],
                    ],
                ];
            },
            $entities
        ));
    }

    private function getEntityManager(array $entities): array
    {
        $result = array_filter(array_map(
            function (Entity $entity) {
                if (!$entity->withStore()) {
                    return [];
                }

                return [
                    self::N => 'item',
                    self::A => [
                        self::N => $entity->getNamespace() . '\\Api\\Data\\' . $entity->getEntityName() . 'Interface',
                        'xsi:type' => 'array',
                    ],
                    self::V => [
                        [
                            self::N => 'item',
                            self::A => [
                                self::N => 'entityTableName',
                                'xsi:type' => 'string',
                            ],
                            self::V => $entity->getTablename(),
                        ],
                        [
                            self::N => 'item',
                            self::A => [
                                self::N => 'identifierField',
                                'xsi:type' => 'string',
                            ],
                            self::V => $entity->getPrimaryKey(),
                        ],
                    ],
                ];
            },
            $entities
        ));

        if (empty($result)) {
            return [];
        }

        return [
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
                        'value' => $result,
                    ],
                ],
            ],
        ];
    }

    private function getEntityExtension(array $entities): array
    {
        $result = array_filter(array_map(
            function (Entity $entity) {
                if (!$entity->withStore()) {
                    return [];
                }

                return [
                    self::N => 'item',
                    self::A => [
                        self::N => $entity->getNamespace() . '\\Api\\Data\\' . $entity->getEntityName() . 'Interface',
                        'xsi:type' => 'array',
                    ],
                    self::V => [
                        [
                            self::N => 'item',
                            self::A => [
                                self::N => 'read',
                                'xsi:type' => 'array',
                            ],
                            self::V => [
                                [
                                    self::N => 'item',
                                    self::A => [
                                        self::N => 'storeReader',
                                        'xsi:type' => 'string',
                                    ],
                                    self::V => $entity->getNamespace() . '\\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Relation\Store\ReadHandler',
                                ],
                            ],
                        ],
                        [
                            self::N => 'item',
                            self::A => [
                                self::N => 'create',
                                'xsi:type' => 'array',
                            ],
                            self::V => [
                                [
                                    self::N => 'item',
                                    self::A => [
                                        self::N => 'storeCreator',
                                        'xsi:type' => 'string',
                                    ],
                                    self::V => $entity->getNamespace() . '\\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Relation\Store\SaveHandler',
                                ],
                            ],
                        ],
                        [
                            self::N => 'item',
                            self::A => [
                                self::N => 'update',
                                'xsi:type' => 'array',
                            ],
                            self::V => [
                                [
                                    self::N => 'item',
                                    self::A => [
                                        self::N => 'storeUpdater',
                                        'xsi:type' => 'string',
                                    ],
                                    self::V => $entity->getNamespace() . '\\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Relation\Store\SaveHandler',
                                ],
                            ],
                        ],
                    ],
                ];
            },
            $entities
        ));

        if (empty($result)) {
            return [];
        }

        return [
            self::N => 'type',
            self::A => [
                'name' => 'Magento\Framework\EntityManager\Operation\ExtensionPool',
            ],
            self::V => [
                'arguments' => [
                    'argument' => [
                        self::A => [
                            'name' => 'extensionActions',
                            'xsi:type' => 'array',
                        ],
                        'value' => $result,
                    ],
                ],
            ],
        ];
    }

    private function getEntityHydrator(array $entities): array
    {
        $result = array_filter(array_map(
            function (Entity $entity) {
                if (!$entity->withStore()) {
                    return [];
                }

                return [
                    self::N => 'item',
                    self::A => [
                        self::N => $entity->getNamespace() . '\\Api\\Data\\' . $entity->getEntityName() . 'Interface',
                        'xsi:type' => 'string',
                    ],
                    self::V => 'Magento\Framework\EntityManager\AbstractModelHydrator',
                ];
            },
            $entities
        ));

        if (empty($result)) {
            return [];
        }

        return [
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
                        'value' => $result,
                    ],
                ],
            ],
        ];
    }
}
