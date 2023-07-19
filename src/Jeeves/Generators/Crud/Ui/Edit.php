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
                'nullable' => false,
            ];
        }

        $fieldset = [];
        foreach ($fields as $name => $param) {
            $fieldset[] = $this->getField($name, $param, $entity, $primary);
        }

        return $service->write('form', function ($writer) use ($uiComponent, $dataSource, $submit, $provider, $fieldset, $primary) {
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd'
            );
            $writer->setIndentString(self::TAB);
            $writer->write([
                [
                    'argument' => [
                        self::A => [
                            self::N => 'data',
                            'xsi:type' => 'array',
                        ],
                        self::V => [
                            [
                                'item' => [
                                    self::A => [
                                        self::N => 'js_config',
                                        'xsi:type' => 'array',
                                    ],
                                    self::V => [
                                        [
                                            self::N => 'item',
                                            self::A => [
                                                self::N => 'provider',
                                                'xsi:type' => 'string',
                                            ],
                                            self::V => $uiComponent . '.' . $dataSource,
                                        ],
                                    ],
                                ],
                                [
                                    self::N => 'item',
                                    self::A => [
                                        self::N => 'label',
                                        'xsi:type' => 'string',
                                        'translate' => 'true',
                                    ],
                                    self::V => 'General Information',
                                ],
                                [
                                    self::N => 'item',
                                    self::A => [
                                        self::N => 'template',
                                        'xsi:type' => 'string',
                                    ],
                                    self::V => 'templates/form/collapsible',
                                ],
                            ],
                        ],
                    ],
                    'settings' => [
                        'buttons' => [
                            [
                                self::N => 'button',
                                self::A => [
                                    self::N => 'save_and_continue',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\SaveAndContinueButton',
                                ],
                            ],
                            [
                                self::N => 'button',
                                self::A => [
                                    self::N => 'save',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\SaveButton',
                                ],
                            ],
                            [
                                self::N => 'button',
                                self::A => [
                                    self::N => 'reset',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\ResetButton',
                                ],
                            ],
                            [
                                self::N => 'button',
                                self::A => [
                                    self::N => 'delete',
                                    'class' => 'Mygento\Base\Block\Adminhtml\Component\Edit\DeleteButton',
                                ],
                            ],
                            [
                                self::N => 'button',
                                self::A => [
                                    self::N => 'back',
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
                        self::A => [
                            self::N => $dataSource,
                        ],
                        self::V => [
                            'argument' => [
                                self::A => [
                                    self::N => 'data',
                                    'xsi:type' => 'array',
                                ],
                                self::V => [
                                    [
                                        'item' => [
                                            self::A => [
                                                self::N => 'js_config',
                                                'xsi:type' => 'array',
                                            ],
                                            self::V => [
                                                [
                                                    'item' => [
                                                        self::A => [
                                                            self::N => 'component',
                                                            'xsi:type' => 'string',
                                                        ],
                                                        self::V => 'Magento_Ui/js/form/provider',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'settings' => [
                                'submitUrl' => [
                                    self::A => [
                                        'path' => $submit,
                                    ],
                                ],
                            ],
                            'dataProvider' => [
                                self::A => [
                                    self::N => $dataSource,
                                    'class' => $provider,
                                ],
                                self::V => [
                                    'settings' => [
                                        'requestFieldName' => 'id',
                                        'primaryFieldName' => $primary,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'fieldset' => [
                        self::A => [
                            self::N => 'general',
                        ],
                        self::V => array_merge([
                            'settings' => [
                                'label' => '',
                            ],
                        ], $fieldset),
                    ],
                ],
            ]);
        });
    }

    private function getField(string $name, array $param, string $entity, string $primary): array
    {
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
            self::N => 'field',
            self::A => [
                self::N => $this->camelCaseToSnakeCase($name),
                'formElement' => $formElement,
            ],
            self::V => [
                'argument' => [
                    self::A => [
                        self::N => 'data',
                        'xsi:type' => 'array',
                    ],
                    self::V => [
                        'item' => [
                            self::A => [
                                self::N => 'config',
                                'xsi:type' => 'array',
                            ],
                            self::V => [
                                'item' => [
                                    self::A => [
                                        self::N => 'source',
                                        'xsi:type' => 'string',
                                    ],
                                    self::V => str_replace('_', '', $this->camelCaseToSnakeCase($entity)),
                                ],
                            ],
                        ],
                    ],
                ],
                'settings' => [
                    'dataType' => $dataType,
                    'label' => [
                        self::A => [
                            'translate' => 'true',
                        ],
                        self::V => $this->snakeCaseToUpperCamelCaseWithSpace($name),
                    ],
                    'visible' => $visible ? 'true' : 'false',
                    'dataScope' => $this->camelCaseToSnakeCase($name),
                ],
            ],
        ];

        return $this->setValidation(
            $this->setFormElements($field, $param),
            $param,
            $notNullable,
            $visible
        );
    }

    private function setFormElements(array $field, array $param): array
    {
        switch ($param['type']) {
            case 'bool':
            case 'boolean':
                $field[self::V]['formElements'] = [
                    'checkbox' => [
                        'settings' => [
                            'valueMap' => [
                                [
                                    [
                                        'map' => [
                                            self::A => [
                                                self::N => 'false',
                                                'xsi:type' => 'number',
                                            ],
                                            self::V => 0,
                                        ],
                                    ],
                                    [
                                        'map' => [
                                            self::A => [
                                                self::N => 'true',
                                                'xsi:type' => 'number',
                                            ],
                                            self::V => 1,
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
                $field[self::A]['class'] = 'Magento\Store\Ui\Component\Form\Field\StoreView';
                $field[self::V]['formElements'] = [
                    'multiselect' => [
                        'settings' => [
                            'options' => [
                                self::A => [
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
            $field[self::V]['formElements'] = [
                'select' => [
                    'settings' => [
                        'options' => [
                            self::A => [
                                'class' => $param['source'],
                            ],
                        ],
                        'caption' => [
                            self::A => [
                                'translate' => 'true',
                            ],
                            self::V => '-- Please Select --',
                        ],
                    ],
                ],
            ];
        }

        return $field;
    }

    private function setValidation(array $field, array $param, bool $notNullable, bool $visible): array
    {
        if (!$visible) {
            return $field;
        }
        $rules = [];
        switch ($param['type']) {
            case 'bool':
            case 'boolean':
                return $field;
            case 'smallint':
            case 'bigint':
            case 'tinyint':
            case 'int':
                $rules[] = [
                    self::N => 'rule',
                    self::A => [
                        self::N => 'validate-integer',
                        'xsi:type' => 'boolean',
                    ],
                    self::V => 'true',
                ];
                if (isset($param['unsigned']) && $param['unsigned'] === true) {
                    $rules[] = [
                        self::N => 'rule',
                        self::A => [
                            self::N => 'validate-zero-or-greater',
                            'xsi:type' => 'boolean',
                        ],
                        self::V => 'true',
                    ];
                }
                break;
            default:
                break;
        }

        if ($notNullable) {
            $rules[] = [
                self::N => 'rule',
                self::A => [
                    self::N => 'required-entry',
                    'xsi:type' => 'boolean',
                ],
                self::V => 'true',
            ];
        }

        if (!empty($rules)) {
            $field[self::V]['settings']['validation'] = $rules;
        }

        return $field;
    }
}
