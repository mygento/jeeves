<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;

class Edit extends Common
{
    private const IGNORED_FIELDS = [
        'created_at',
        'updated_at',
    ];

    public function generateAdminUiForm(
        string $uiComponent,
        string $dataSource,
        string $submit,
        string $provider,
        string $entity,
        string $primary,
        array $fields = self::DEFAULT_FIELDS,
        bool $withStore = false
    ): string {
        $service = $this->getService();
        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }

        $fieldset = array_map(
            function ($name, $param) use ($entity, $primary) {
                $notNullable = isset($param['nullable']) && $param['nullable'] === false;

                $visible = true;
                if ($primary === $this->camelCaseToSnakeCase($name)) {
                    $visible = false;
                }

                if (in_array($name, self::IGNORED_FIELDS)) {
                    $visible = false;
                    $notNullable = false;
                }

                switch ($param['type']) {
                    case 'text':
                    case 'mediumtext':
                    case 'longtext':
                        $dataType = 'text';
                        $formElement = 'textarea';
                        break;
                    case 'store':
                        $dataType = 'int';
                        $formElement = 'multiselect';
                        break;
                    case 'smallint':
                    case 'bigint':
                    case 'tinyint':
                    case 'int':
                        $dataType = 'text';
                        $formElement = 'input';
                        break;
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                        $dataType = 'date';
                        $formElement = 'input';
                        break;
                    case 'bool':
                    case 'boolean':
                        $dataType = 'boolean';
                        $formElement = 'checkbox';
                        break;
                    case 'price':
                        $dataType = 'price';
                        $formElement = 'input';
                        break;
                    default:
                        $dataType = 'text';
                        $formElement = 'input';
                }
                if (isset($param['source'])) {
                    $formElement = 'select';
                }
                $field = [
                    'name' => 'field',
                    'attributes' => [
                        'name' => $this->camelCaseToSnakeCase($name),
                        'formElement' => $formElement,
                    ],
                    'value' => [
                        'argument' => [
                            'attributes' => [
                                'name' => 'data',
                                'xsi:type' => 'array',
                            ],
                            'value' => [
                                'item' => [
                                    'attributes' => [
                                        'name' => 'config',
                                        'xsi:type' => 'array',
                                    ],
                                    'value' => [
                                        'item' => [
                                            'attributes' => [
                                                'name' => 'source',
                                                'xsi:type' => 'string',
                                            ],
                                            'value' => str_replace('_', '', $this->camelCaseToSnakeCase($entity)),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'settings' => [
                            'dataType' => $dataType,
                            'label' => [
                                'attributes' => [
                                    'translate' => 'true',
                                ],
                                'value' => $this->snakeCaseToUpperCamelCaseWithSpace($name),
                            ],
                            'visible' => $visible ? 'true' : 'false',
                            'dataScope' => $this->camelCaseToSnakeCase($name),
                        ],
                    ],
                ];
                switch ($param['type']) {
                    case 'bool':
                    case 'boolean':
                        $field['value']['formElements'] = [
                            'checkbox' => [
                                'settings' => [
                                    'valueMap' => [
                                        [
                                            [
                                                'map' => [
                                                    'attributes' => [
                                                        'name' => 'false',
                                                        'xsi:type' => 'number',
                                                    ],
                                                    'value' => 0,
                                                ],
                                            ],
                                            [
                                                'map' => [
                                                    'attributes' => [
                                                        'name' => 'true',
                                                        'xsi:type' => 'number',
                                                    ],
                                                    'value' => 1,
                                                ],
                                            ],
                                        ],
                                    ],
                                    'prefer' => 'toggle',
                                ],
                            ],
                        ];
                        break;
                    case 'store':
                        $field['attributes']['class'] = 'Magento\Store\Ui\Component\Form\Field\StoreView';
                        $field['value']['formElements'] = [
                            'multiselect' => [
                                'settings' => [
                                    'options' => [
                                        'attributes' => [
                                            'class' => 'Magento\Store\Ui\Component\Listing\Column\Store\Options',
                                        ],
                                    ],
                                ],
                            ],
                        ];
                        break;
                    default:
                        if ($notNullable) {
                            $field['value']['settings']['validation']['rule'] = [
                                'attributes' => [
                                    'name' => 'required-entry',
                                    'xsi:type' => 'boolean',
                                ],
                                'value' => 'true',
                            ];
                        }
                        break;
                }
                if (isset($param['source'])) {
                    $field['value']['formElements'] = [
                        'select' => [
                            'settings' => [
                                'options' => [
                                    'attributes' => [
                                        'class' => $param['source'],
                                    ],
                                ],
                                'caption' => [
                                    'attributes' => [
                                        'translate' => 'true',
                                    ],
                                    'value' => '-- Please Select --',
                                ],
                            ],
                        ],
                    ];
                }

                return $field;
            },
            array_keys($fields),
            $fields
        );

        return $service->write('form', function ($writer) use ($uiComponent, $dataSource, $submit, $provider, $fieldset, $primary) {
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd'
            );
            $writer->setIndentString(self::TAB);
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
                                        [
                                            'name' => 'item',
                                            'attributes' => [
                                                'name' => 'provider',
                                                'xsi:type' => 'string',
                                            ],
                                            'value' => $uiComponent . '.' . $dataSource,
                                        ],
                                    ],
                                ],
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => 'label',
                                        'xsi:type' => 'string',
                                        'translate' => 'true',
                                    ],
                                    'value' => 'General Information',
                                ],
                                [
                                    'name' => 'item',
                                    'attributes' => [
                                        'name' => 'template',
                                        'xsi:type' => 'string',
                                    ],
                                    'value' => 'templates/form/collapsible',
                                ],
                            ],
                        ],
                    ],
                    'settings' => [
                        'buttons' => [
                            [
                                'name' => 'button',
                                'attributes' => [
                                    'name' => 'save_and_continue',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\SaveAndContinueButton',
                                ],
                            ],
                            [
                                'name' => 'button',
                                'attributes' => [
                                    'name' => 'save',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\SaveButton',
                                ],
                            ],
                            [
                                'name' => 'button',
                                'attributes' => [
                                    'name' => 'reset',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\ResetButton',
                                ],
                            ],
                            [
                                'name' => 'button',
                                'attributes' => [
                                    'name' => 'delete',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\DeleteButton',
                                ],
                            ],
                            [
                                'name' => 'button',
                                'attributes' => [
                                    'name' => 'back',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\BackButton',
                                ],
                            ],
                        ],
                        'namespace' => $uiComponent,
                        'dataScope' => 'data',
                        'deps' => [
                            'dep' => $uiComponent . '.' . $dataSource,
                        ],
                    ],
                    'dataSource' => [
                        'attributes' => [
                            'name' => $dataSource,
                        ],
                        'value' => [
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
                                                [
                                                    'item' => [
                                                        'attributes' => [
                                                            'name' => 'component',
                                                            'xsi:type' => 'string',
                                                        ],
                                                        'value' => 'Magento_Ui/js/form/provider',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'settings' => [
                                'submitUrl' => [
                                    'attributes' => [
                                        'path' => $submit,
                                    ],
                                ],
                            ],
                            'dataProvider' => [
                                'attributes' => [
                                    'name' => $dataSource,
                                    'class' => $provider,
                                ],
                                'value' => [
                                    'settings' => [
                                        'requestFieldName' => $primary,
                                        'primaryFieldName' => $primary,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'fieldset' => [
                        'attributes' => [
                            'name' => 'general',
                        ],
                        'value' => array_merge([
                            'settings' => [
                                'label' => '',
                            ],
                        ], $fieldset),
                    ],
                ],
            ]);
        });
    }
}
