<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;

class Listing extends Common
{
    private const READONLY_FIELDS = [
        'created_at',
        'updated_at',
    ];

    public function generateAdminUiIndex(
        string $uiComponent,
        string $dataSource,
        string $column,
        string $addNew,
        string $acl,
        string $actions,
        string $inline,
        string $massDelete,
        string $select,
        string $editor,
        string $primaryKey,
        array $fields = self::DEFAULT_FIELDS,
        bool $readonly = false,
        bool $withStore = false
    ): string {
        $service = $this->getService();
        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }
        $columns = array_map(
            function ($name, $param) use ($primaryKey) {
                $notNullable = isset($param['nullable']) && $param['nullable'] === false;
                $options = null;
                switch ($param['type']) {
                    case 'bool':
                    case 'boolean':
                        $filter = 'select';
                        $dataType = 'select';
                        $options = 'Magento\Config\Model\Config\Source\Yesno';
                        break;
                    case 'smallint':
                    case 'bigint':
                    case 'tinyint':
                    case 'int':
                        $filter = 'textRange';
                        $dataType = 'text';

                        break;
                    case 'price':
                        $filter = 'textRange';
                        $dataType = 'text';
                        break;
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                        $filter = 'dateRange';
                        $dataType = 'date';
                        break;
                    default:
                        $filter = 'text';
                        $dataType = 'text';
                }
                if (isset($param['source'])) {
                    $filter = 'select';
                    $dataType = 'select';
                }
                $col = [
                    self::N => 'column',
                    self::A => [
                        'name' => $name,
                    ],
                    self::V => [
                        'settings' => [
                            'filter' => $filter,
                            'dataType' => $dataType,
                            'editor' => [
                                'editorType' => $dataType,
                            ],
                            'label' => [
                                self::A => [
                                    'translate' => 'true',
                                ],
                                self::V => $this->snakeCaseToUpperCamelCaseWithSpace($name),
                            ],
                        ],
                    ],
                ];
                if ($primaryKey === $name) {
                    unset($col[self::V]['settings']['editor']);
                    $col[self::V]['settings']['sorting'] = 'asc';
                }
                switch ($param['type']) {
                    case 'bool':
                    case 'boolean':
                        $col[self::A]['component'] = 'Magento_Ui/js/grid/columns/select';
                        $col[self::V]['settings']['options'] = [
                            self::A => [
                                'class' => $options,
                            ],
                        ];
                        break;
                    case 'price':
                        $col[self::A]['class'] = 'Magento\Catalog\Ui\Component\Listing\Columns\Price';
                        break;
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                        $col[self::A]['class'] = 'Magento\Ui\Component\Listing\Columns\Date';
                        $col[self::A]['component'] = 'Magento_Ui/js/grid/columns/date';
                        break;
                    case 'store':
                        $col[self::A]['class'] = 'Magento\Store\Ui\Component\Listing\Column\Store';
                        $col[self::V]['settings']['label'][self::V] = 'Store View';
                        $col[self::V]['settings']['bodyTmpl'] = 'ui/grid/cells/html';
                        $col[self::V]['settings']['sortable'] = 'false';
                        unset($col[self::V]['settings']['filter']);
                        unset($col[self::V]['settings']['dataType']);
                        unset($col[self::V]['settings']['editor']);
                        break;
                    default:
                        break;
                }
                if ($notNullable) {
                    $col[self::V]['settings']['editor']['validation']['rule'] = [
                        self::A => [
                            'name' => 'required-entry',
                            'xsi:type' => 'boolean',
                        ],
                        self::V => 'true',
                    ];
                }
                if (isset($param['source'])) {
                    $col[self::A]['component'] = 'Magento_Ui/js/grid/columns/select';
                    $col[self::V]['settings']['options'] = [
                        self::A => [
                            'class' => $param['source'],
                        ],
                    ];
                }

                if (in_array($name, self::READONLY_FIELDS)) {
                    unset($col[self::V]['settings']['editor']);
                }

                return $col;
            },
            array_keys($fields),
            $fields
        );

        return $service->write('listing', function ($writer) use (
            $columns,
            $uiComponent,
            $dataSource,
            $column,
            $addNew,
            $acl,
            $actions,
            $inline,
            $massDelete,
            $select,
            $editor,
            $readonly,
            $primaryKey
        ) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd'
            );

            $actionColumn = $readonly ? [] : [
                'actionsColumn' => [
                    self::A => [
                        'name' => 'actions',
                        'class' => $actions,
                    ],
                    self::V => [
                        'settings' => [
                            'indexField' => $primaryKey,
                        ],
                    ],
                ],
            ];

            $addNewButton = $readonly ? [] : [
                'button' => [
                    self::A => [
                        'name' => 'add',
                    ],
                    self::V => [
                        'url' => [
                            self::A => [
                                'path' => '*/*/new',
                            ],
                        ],
                        'class' => 'primary',
                        'label' => [
                            self::A => [
                                'translate' => 'true',
                            ],
                            self::V => $addNew,
                        ],
                    ],
                ],
            ];

            $gridColumns = array_merge($readonly ? [] : [
                'settings' => $this->getListingSettings($inline, $select, $editor, $primaryKey),
                'selectionsColumn' => [
                    self::A => [
                        'name' => 'ids',
                    ],
                    self::V => [
                        'settings' => [
                            'indexField' => $primaryKey,
                        ],
                    ],
                ],
            ], $columns, $actionColumn);

            $writer->write([
                [
                    'argument' => [
                        self::A => [
                            'name' => 'data',
                            'xsi:type' => 'array',
                        ],
                        self::V => [
                            [
                                'item' => [
                                    self::A => [
                                        'name' => 'js_config',
                                        'xsi:type' => 'array',
                                    ],
                                    self::V => [
                                        'item' => [
                                            self::A => [
                                                'name' => 'provider',
                                                'xsi:type' => 'string',
                                            ],
                                            self::V => $uiComponent . '.' . $dataSource,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'settings' => [
                        'buttons' => $addNewButton,
                        'spinner' => $column,
                        'deps' => [
                            'dep' => $uiComponent . '.' . $dataSource,
                        ],
                    ],
                    'dataSource' => [
                        self::A => [
                            'name' => $dataSource,
                            'component' => 'Magento_Ui/js/grid/provider',
                        ],
                        self::V => [
                            'settings' => [
                                'storageConfig' => [
                                    'param' => [
                                        self::A => [
                                            'name' => 'indexField',
                                            'xsi:type' => 'string',
                                        ],
                                        self::V => $primaryKey,
                                    ],
                                ],
                                'updateUrl' => [
                                    self::A => [
                                        'path' => 'mui/index/render',
                                    ],
                                ],
                            ],
                            'aclResource' => $acl,
                            'dataProvider' => [
                                self::A => [
                                    'class' => 'Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider',
                                    'name' => $dataSource,
                                ],
                                self::V => [
                                    'settings' => [
                                        'requestFieldName' => 'id',
                                        'primaryFieldName' => $primaryKey,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'listingToolbar' => $this->getToolbar($massDelete, $editor, $readonly),
                    'columns' => [
                        self::A => [
                            'name' => $column,
                        ],
                        self::V => $gridColumns,
                    ],
                ],
            ]);
        });
    }

    private function getListingSettings(
        string $inline,
        string $select,
        string $editor,
        string $primaryKey
    ) {
        return [
            'editorConfig' => [
                [
                    'name' => 'param',
                    self::A => [
                        'name' => 'clientConfig',
                        'xsi:type' => 'array',
                    ],
                    self::V => [
                        [
                            'name' => 'item',
                            self::A => [
                                'name' => 'saveUrl',
                                'xsi:type' => 'url',
                                'path' => $inline,
                            ],
                        ],
                        [
                            'name' => 'item',
                            self::A => [
                                'name' => 'validateBeforeSave',
                                'xsi:type' => 'boolean',
                            ],
                            self::V => 'false',
                        ],
                    ],
                ],
                [
                    'name' => 'param',
                    self::A => [
                        'name' => 'indexField',
                        'xsi:type' => 'string',
                    ],
                    self::V => $primaryKey,
                ],
                [
                    'name' => 'param',
                    self::A => [
                        'name' => 'enabled',
                        'xsi:type' => 'boolean',
                    ],
                    self::V => 'true',
                ],
                [
                    'name' => 'param',
                    self::A => [
                        'name' => 'selectProvider',
                        'xsi:type' => 'string',
                    ],
                    self::V => $select,
                ],
            ],
            'childDefaults' => [
                'param' => [
                    self::A => [
                        'name' => 'fieldAction',
                        'xsi:type' => 'array',
                    ],
                    self::V => [
                        [
                            'name' => 'item',
                            self::A => [
                                'name' => 'provider',
                                'xsi:type' => 'string',
                            ],
                            self::V => $editor,
                        ],
                        [
                            'name' => 'item',
                            self::A => [
                                'name' => 'target',
                                'xsi:type' => 'string',
                            ],
                            self::V => 'startEdit',
                        ],
                        [
                            'name' => 'item',
                            self::A => [
                                'name' => 'params',
                                'xsi:type' => 'array',
                            ],
                            self::V => [
                                [
                                    'name' => 'item',
                                    self::A => [
                                        'name' => '0',
                                        'xsi:type' => 'string',
                                    ],
                                    self::V => '${ $.$data.rowIndex }',
                                ],
                                [
                                    'name' => 'item',
                                    self::A => [
                                        'name' => '1',
                                        'xsi:type' => 'boolean',
                                    ],
                                    self::V => 'true',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function getToolbar($massDelete, $editor, $readonly)
    {
        $massActions = $readonly ? [] : [
            'massaction' => [
                self::A => [
                    'name' => 'listing_massaction',
                ],
                self::V => [
                    [
                        'name' => 'action',
                        self::A => [
                            'name' => 'delete',
                        ],
                        self::V => [
                            'settings' => [
                                'confirm' => [
                                    'message' => [
                                        self::A => [
                                            'translate' => 'true',
                                        ],
                                        self::V => 'Are you sure you want to delete selected items?',
                                    ],
                                    'title' => [
                                        self::A => [
                                            'translate' => 'true',
                                        ],
                                        self::V => 'Delete items',
                                    ],
                                ],
                                'url' => [
                                    self::A => [
                                        'path' => $massDelete,
                                    ],
                                ],
                                'type' => 'delete',
                                'label' => [
                                    self::A => [
                                        'translate' => 'true',
                                    ],
                                    self::V => 'Delete',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'action',
                        self::A => [
                            'name' => 'edit',
                        ],
                        self::V => [
                            'settings' => [
                                'callback' => [
                                    'target' => 'editSelected',
                                    'provider' => $editor,
                                ],
                                'type' => 'edit',
                                'label' => [
                                    self::A => [
                                        'translate' => 'true',
                                    ],
                                    self::V => 'Edit',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return [
            self::A => [
                'name' => 'listing_top',
            ],
            self::V => [
                'settings' => [
                    'sticky' => true,
                ],
                'bookmark' => [
                    self::A => [
                        'name' => 'bookmarks',
                    ],
                ],
                'columnsControls' => [
                    self::A => [
                        'name' => 'columns_controls',
                    ],
                ],
                'filterSearch' => [
                    self::A => [
                        'name' => 'fulltext',
                    ],
                ],
                'filters' => [
                    self::A => [
                        'name' => 'listing_filters',
                    ],
                    self::V => [
                        'settings' => [
                            'templates' => [
                                'filters' => [
                                    'select' => [
                                        [
                                            'name' => 'param',
                                            self::A => [
                                                'name' => 'template',
                                                'xsi:type' => 'string',
                                            ],
                                            self::V => 'ui/grid/filters/elements/ui-select',
                                        ],
                                        [
                                            'name' => 'param',
                                            self::A => [
                                                'name' => 'component',
                                                'xsi:type' => 'string',
                                            ],
                                            self::V => 'Magento_Ui/js/form/element/ui-select',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                $massActions,
                'paging' => [
                    self::A => [
                        'name' => 'listing_paging',
                    ],
                ],
            ],
        ];
    }
}
