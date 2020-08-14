<?php

namespace Mygento\Jeeves\Util\Xml;

class Shipping
{
    const A = 'attributes';
    const N = 'name';
    const V = 'value';
    const YN = 'Magento\Config\Model\Config\Source\Yesno';

    public function getEnabled(): array
    {
        return $this->dropdown('active', 'Enabled', self::YN, [], ['sortOrder' => '10']);
    }

    public function getTest(): array
    {
        return $this->dropdown('test', 'Test mode', self::YN, ['active' => 1], ['sortOrder' => '90']);
    }

    public function getSort(): array
    {
        return [
            self::N => 'field',
            self::A => $this->field([
                'id' => 'sort_order',
                'type' => 'text',
                'sortOrder' => '89',
            ]),
            self::V => [
                'label' => 'Sort Order',
                'validate' => 'validate-number validate-zero-or-greater',
            ],
        ];
    }

    public function getDebug(): array
    {
        return $this->dropdown('debug', 'Debug', self::YN, [], ['sortOrder' => '99']);
    }

    public function getTitle(): array
    {
        return [
            self::N => 'field',
            self::A => $this->field([
                'id' => 'title',
                'type' => 'text',
                'sortOrder' => '20',
            ]),
            self::V => [
                'label' => 'Method Title',
                'validate' => 'required-entry',
            ],
        ];
    }

    public function getAuthGroup(array $child = []): array
    {
        return [
            self::N => 'group',
            self::A => $this->field([
                'id' => 'auth',
                'type' => 'text',
                'sortOrder' => '100',
            ]),
            self::V => array_merge(['label' => 'Authentication'], $child),
        ];
    }

    public function getOptionsGroup(array $child = []): array
    {
        return [
            self::N => 'group',
            self::A => $this->field([
                'id' => 'options',
                'type' => 'text',
                'sortOrder' => '110',
            ]),
            self::V => array_merge(['label' => 'Shipping Options'], $child),
        ];
    }

    public function getOrderStatusGroup(): array
    {
        return [
            self::N => 'group',
            self::A => $this->field([
                'id' => 'order_statuses',
                'type' => 'text',
                'sortOrder' => '140',
            ]),
            self::V => [
                ['label' => 'Order Statuses'],
                $this->dropdown('autoshipping', 'Enable autoship by status', self::YN),
                $this->dropdown(
                    'autoshipping_statuses',
                    'Enable autoship by status',
                    'Mygento\Base\Model\Source\Status',
                    ['autoshipping' => 1]
                ),
                $this->dropdown(
                    'shipment_success_status',
                    'Order status after successful shipment',
                    'Mygento\Base\Model\Source\Status'
                ),
                $this->dropdown(
                    'shipment_fail_status',
                    'Order status after failed shipment',
                    'Mygento\Base\Model\Source\Status'
                ),
                $this->dropdown(
                    'track_check',
                    'Enable Track Check',
                    self::YN
                ),
                [
                    self::N => 'field',
                    self::A => $this->field([
                        'id' => 'track_cron',
                        'type' => 'text',
                    ]),
                    self::V => [
                        'label' => 'Track Check Cron',
                        'depends' => [
                            'field' => [
                                self::A => ['id' => 'track_check'],
                                self::V => '1',
                            ],
                        ],
                    ],
                ],
                $this->multiselect(
                    'track_statuses',
                    'Track Check Statuses',
                    'Mygento\Base\Model\Source\Status',
                    [
                        'track_check' => 1,
                    ]
                ),
            ],
        ];
    }

    public function getPackageGroup(string $method): array
    {
        return [
            self::N => 'group',
            self::A => $this->field([
                'id' => 'package',
                'type' => 'text',
                'sortOrder' => '120',
            ]),
            self::V => [
                ['label' => 'Packaging'],
                $this->dropdown(
                    'weight_unit',
                    'Weight Unit',
                    'Mygento\Shipment\Model\Source\Weightunits',
                    [],
                    [],
                    ['config_path' => 'carriers/' . $method . '/weight_unit']
                ),
                $this->dropdown(
                    'dimension_unit',
                    'Dimension Unit',
                    'Mygento\Shipment\Model\Source\Dimensionunits',
                    [],
                    [],
                    ['config_path' => 'carriers/' . $method . '/dimension_unit']
                ),
                $this->WLH($method),
            ],
        ];
    }

    public function getTaxGroup(string $namespace): array
    {
        return [
            self::N => 'group',
            self::A => $this->field([
                'id' => 'tax_options',
                'type' => 'text',
                'sortOrder' => '130',
            ]),
            self::V => [
                'label' => 'Tax',
                $this->dropdown('tax', 'Enabled', self::YN),
                $this->dropdown(
                    'tax_same',
                    'Same tax for all products',
                    self::YN,
                    ['tax' => 1]
                ),
                $this->dropdown(
                    'tax_products',
                    'Tax value for all products',
                    $namespace . '\Model\Source\Tax',
                    ['tax' => 1, 'tax_same' => 1]
                ),
                $this->dropdown(
                    'tax_product_attr',
                    'Product Tax Attribute',
                    'Mygento\Base\Model\Source\Attributes',
                    ['tax' => 1, 'tax_same' => 0]
                ),
                $this->dropdown(
                    'tax_shipping',
                    'Shipping Tax',
                    $namespace . '\Model\Source\Tax',
                    ['tax' => 1]
                ),
            ],
        ];
    }

    private function WLH(string $method): array
    {
        $attr = ['width', 'length', 'height'];
        $result = [];
        foreach ($attr as $p) {
            $result[] = $this->dropdown(
                $p,
                ucfirst($p) . ' Attribute',
                'Mygento\Base\Model\Source\Attributes',
                [],
                [],
                ['config_path' => 'carriers/' . $method . '/' . $p]
            );
            $result[] = [
                self::N => 'field',
                self::A => $this->field([
                    'id' => $p . '_default',
                    'type' => 'text',
                ]),
                self::V => [
                    'label' => ucfirst($p) . ' default',
                    'validate' => 'validate-number',
                    'config_path' => 'carriers/' . $method . '/' . $p . '_default',
                    'depends' => [
                        'field' => [
                            self::A => ['id' => $p],
                            self::V => '0',
                        ],
                    ],
                ],
            ];
        }

        return $result;
    }

    private function field(
        array $field,
        bool $default = true,
        bool $website = true,
        bool $store = true
    ): array {
        $field['translate'] = 'label';
        $field['showInDefault'] = $default ? '1' : '0';
        $field['showInWebsite'] = $website ? '1' : '0';
        $field['showInStore'] = $store ? '1' : '0';

        return $field;
    }

    private function dropdown(
        string $id,
        string $label,
        string $source,
        array $depends = [],
        array $attr = [],
        array $other = []
    ): array {
        $value = [
            'label' => $label,
            'source_model' => $source,
        ];
        if (!empty($depends)) {
            foreach ($depends as $d => $v) {
                $value['depends'][] = [
                    self::N => 'field',
                    self::A => ['id' => $d],
                    self::V => (string) $v,
                ];
            }
        }

        return [
            self::N => 'field',
            self::A => $this->field(array_merge([
                'id' => $id,
                'type' => 'select',
            ], $attr)),
            self::V => array_merge($value, $other),
        ];
    }

    private function multiselect(
        string $id,
        string $label,
        string $source,
        array $depends = [],
        array $attr = [],
        array $other = []
    ): array {
        $value = [
            'label' => $label,
            'source_model' => $source,
        ];
        if (!empty($depends)) {
            foreach ($depends as $d => $v) {
                $value['depends'][] = [
                    self::N => 'field',
                    self::A => ['id' => $d],
                    self::V => (string) $v,
                ];
            }
        }

        return [
            self::N => 'field',
            self::A => $this->field(array_merge([
                'id' => $id,
                'type' => 'multiselect',
            ], $attr)),
            self::V => array_merge($value, $other),
        ];
    }
}
