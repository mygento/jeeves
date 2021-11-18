<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;

class Edit extends Common
{
    public function generateAdminUiForm(
        string $uiComponent,
        string $dataSource,
        string $submit,
        string $provider,
        string $entity,
        array $fields = self::DEFAULT_FIELDS,
        $withStore = false
    ): string {
        $service = $this->getService();
        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }
        $fieldset = array_map(
            function ($name, $param) use ($entity) {
                $notNullable = isset($param['nullable']) && $param['nullable'] === false;
                switch ($param['type']) {
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
                            'visible' => 'id' === $this->camelCaseToSnakeCase($name) ? 'false' : 'true',
                            'dataScope' => $this->camelCaseToSnakeCase($name),
                        ],
                    ],
                ];
                switch ($param['type']) {
                    case 'varchar':
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

        return $service->write('form', function ($writer) use ($uiComponent, $dataSource, $submit, $provider, $fieldset) {
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd'
            );
            $writer->setIndentString('    ');
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
                                        'requestFieldName' => 'id',
                                        'primaryFieldName' => 'id',
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
