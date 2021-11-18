<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;

class Listing extends Common
{
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
        array $fields = self::DEFAULT_FIELDS,
        bool $readonly = false
    ): string {
        $service = $this->getService();
        $columns = array_map(
            function ($name, $param) {
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
                    'name' => 'column',
                    'attributes' => [
                        'name' => $name,
                    ],
                    'value' => [
                        'settings' => [
                            'filter' => $filter,
                            'dataType' => $dataType,
                            'editor' => [
                                'editorType' => $dataType,
                            ],
                            'label' => [
                                'attributes' => [
                                    'translate' => 'true',
                                ],
                                'value' => $this->snakeCaseToUpperCamelCaseWithSpace($name),
                            ],
                        ],
                    ],
                ];
                if ('id' === $name) {
                    unset($col['value']['settings']['editor']);
                    $col['value']['settings']['sorting'] = 'asc';
                }
                switch ($param['type']) {
                    case 'varchar':
                        if ($notNullable) {
                            $col['value']['settings']['editor']['validation']['rule'] = [
                                'attributes' => [
                                    'name' => 'required-entry',
                                    'xsi:type' => 'boolean',
                                ],
                                'value' => 'true',
                            ];
                        }
                        break;
                    case 'bool':
                    case 'boolean':
                        $col['attributes']['component'] = 'Magento_Ui/js/grid/columns/select';
                        $col['value']['settings']['options'] = [
                            'attributes' => [
                                'class' => $options,
                            ],
                        ];
                        break;
                    case 'price':
                        $col['attributes']['class'] = 'Magento\Catalog\Ui\Component\Listing\Columns\Price';
                        break;
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                        $col['attributes']['class'] = 'Magento\Ui\Component\Listing\Columns\Date';
                        $col['attributes']['component'] = 'Magento_Ui/js/grid/columns/date';
                        break;
                    default:
                        break;
                }
                if (isset($param['source'])) {
                    $col['attributes']['component'] = 'Magento_Ui/js/grid/columns/select';
                    $col['value']['settings']['options'] = [
                        'attributes' => [
                            'class' => $param['source'],
                        ],
                    ];
                }

                return $col;
            },
            array_keys($fields),
            $fields
        );

        return $service->write('listing', function ($writer) use ($columns, $uiComponent, $dataSource, $column, $addNew, $acl, $actions, $inline, $massDelete, $select, $editor, $readonly) {
            $writer->setIndentString('    ');
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd'
            );

            $actionColumn = $readonly ? [] : [
                'actionsColumn' => [
                    'attributes' => [
                        'name' => 'actions',
                        'class' => $actions,
                    ],
                    'value' => [
                        'settings' => [
                            'indexField' => 'id',
                        ],
                    ],
                ],
            ];

            $addNewButton = $readonly ? [] : [
                'button' => [
                    'attributes' => [
                        'name' => 'add',
                    ],
                    'value' => [
                        'url' => [
                            'attributes' => [
                                'path' => '*/*/new',
                            ],
                        ],
                        'class' => 'primary',
                        'label' => [
                            'attributes' => [
                                'translate' => 'true',
                            ],
                            'value' => $addNew,
                        ],
                    ],
                ],
            ];

            $gridColumns = array_merge($readonly ? [] : [
                'settings' => $this->getListingSettings($inline, $select, $editor),
                'selectionsColumn' => [
                    'attributes' => [
                        'name' => 'ids',
                    ],
                    'value' => [
                        'settings' => [
                            'indexField' => 'id',
                        ],
                    ],
                ],
            ], $columns, $actionColumn);

            $writer->write([
                [
                    'argument' => [
                        'attributes' => [
                            'name' => 'data',
                            'xsi:type' => 'array',
                        ],
                        'value' => [
                            [
                                'item' => [
                                    'attributes' => [
                                        'name' => 'js_config',
                                        'xsi:type' => 'array',
                                    ],
                                    'value' => [
                                        'item' => [
                                            'attributes' => [
                                                'name' => 'provider',
                                                'xsi:type' => 'string',
                                            ],
                                            'value' => $uiComponent . '.' . $dataSource,
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
                        'attributes' => [
                            'name' => $dataSource,
                            'component' => 'Magento_Ui/js/grid/provider',
                        ],
                        'value' => [
                            'settings' => [
                                'storageConfig' => [
                                    'param' => [
                                        'attributes' => [
                                            'name' => 'indexField',
                                            'xsi:type' => 'string',
                                        ],
                                        'value' => 'id',
                                    ],
                                ],
                                'updateUrl' => [
                                    'attributes' => [
                                        'path' => 'mui/index/render',
                                    ],
                                ],
                            ],
                            'aclResource' => $acl,
                            'dataProvider' => [
                                'attributes' => [
                                    'class' => 'Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider',
                                    'name' => $dataSource,
                                ],
                                'value' => [
                                    'settings' => [
                                        'requestFieldName' => 'id',
                                        'primaryFieldName' => 'id',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'listingToolbar' => $this->getToolbar($massDelete, $editor, $readonly),
                    'columns' => [
                        'attributes' => [
                            'name' => $column,
                        ],
                        'value' => $gridColumns,
                    ],
                ],
            ]);
        });
    }

    private function getListingSettings($inline, $select, $editor)
    {
        return [
            'editorConfig' => [
                [
                    'name' => 'param',
                    'attributes' => [
                        'name' => 'clientConfig',
                        'xsi:type' => 'array',
                    ],
                    'value' => [
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'saveUrl',
                                'xsi:type' => 'url',
                                'path' => $inline,
                            ],
                        ],
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'validateBeforeSave',
                                'xsi:type' => 'boolean',
                            ],
                            'value' => 'false',
                        ],
                    ],
                ],
                [
                    'name' => 'param',
                    'attributes' => [
                        'name' => 'indexField',
                        'xsi:type' => 'string',
                    ],
                    'value' => 'id',
                ],
                [
                    'name' => 'param',
                    'attributes' => [
                        'name' => 'enabled',
                        'xsi:type' => 'boolean',
                    ],
                    'value' => 'true',
                ],
                [
                    'name' => 'param',
                    'attributes' => [
                        'name' => 'selectProvider',
                        'xsi:type' => 'string',
                    ],
                    'value' => $select,
                ],
            ],
            'childDefaults' => [
                'param' => [
                    'attributes' => [
                        'name' => 'fieldAction',
                        'xsi:type' => 'array',
                    ],
                    'value' => [
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'provider',
                                'xsi:type' => 'string',
                            ],
                            'value' => $editor,
                        ],
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'target',
                                'xsi:type' => 'string',
                            ],
                            'value' => 'startEdit',
                        ],
                        [
                            'name' => 'item',
                            'attributes' => [
                                'name' => 'params',
                                'xsi:type' => 'array',
                            ],
                            'value' => [
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => '0',
                                        'xsi:type' => 'string',
                                    ],
                                    'value' => '${ $.$data.rowIndex }',
                                ],
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => '1',
                                        'xsi:type' => 'boolean',
                                    ],
                                    'value' => 'true',
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
                'attributes' => [
                    'name' => 'listing_massaction',
                ],
                'value' => [
                    [
                        'name' => 'action',
                        'attributes' => [
                            'name' => 'delete',
                        ],
                        'value' => [
                            'settings' => [
                                'confirm' => [
                                    'message' => [
                                        'attributes' => [
                                            'translate' => 'true',
                                        ],
                                        'value' => 'Are you sure you want to delete selected items?',
                                    ],
                                    'title' => [
                                        'attributes' => [
                                            'translate' => 'true',
                                        ],
                                        'value' => 'Delete items',
                                    ],
                                ],
                                'url' => [
                                    'attributes' => [
                                        'path' => $massDelete,
                                    ],
                                ],
                                'type' => 'delete',
                                'label' => [
                                    'attributes' => [
                                        'translate' => 'true',
                                    ],
                                    'value' => 'Delete',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'action',
                        'attributes' => [
                            'name' => 'edit',
                        ],
                        'value' => [
                            'settings' => [
                                'callback' => [
                                    'target' => 'editSelected',
                                    'provider' => $editor,
                                ],
                                'type' => 'edit',
                                'label' => [
                                    'attributes' => [
                                        'translate' => 'true',
                                    ],
                                    'value' => 'Edit',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return [
            'attributes' => [
                'name' => 'listing_top',
            ],
            'value' => [
                'settings' => [
                    'sticky' => true,
                ],
                'bookmark' => [
                    'attributes' => [
                        'name' => 'bookmarks',
                    ],
                ],
                'columnsControls' => [
                    'attributes' => [
                        'name' => 'columns_controls',
                    ],
                ],
                'filterSearch' => [
                    'attributes' => [
                        'name' => 'fulltext',
                    ],
                ],
                'filters' => [
                    'attributes' => [
                        'name' => 'listing_filters',
                    ],
                    'value' => [
                        'settings' => [
                            'templates' => [
                                'filters' => [
                                    'select' => [
                                        [
                                            'name' => 'param',
                                            'attributes' => [
                                                'name' => 'template',
                                                'xsi:type' => 'string',
                                            ],
                                            'value' => 'ui/grid/filters/elements/ui-select',
                                        ],
                                        [
                                            'name' => 'param',
                                            'attributes' => [
                                                'name' => 'component',
                                                'xsi:type' => 'string',
                                            ],
                                            'value' => 'Magento_Ui/js/form/element/ui-select',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                $massActions,
                'paging' => [
                    'attributes' => [
                        'name' => 'listing_paging',
                    ],
                ],
            ],
        ];
    }
}
