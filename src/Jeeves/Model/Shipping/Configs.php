<?php

namespace Mygento\Jeeves\Model\Shipping;

use Mygento\Jeeves\Generators\Shipping\System;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Configs extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generate(Result $result)
    {
        $this->writeFile(
            $result->getPath() . '/etc/adminhtml/system.xml',
            $this->genSystemXml($result)
        );

        $this->writeFile(
            $result->getPath() . '/etc/config.xml',
            $this->genConfigXml($result)
        );
    }

    private function genConfigXml(Result $result)
    {
        $service = $this->getService();
        $configList = array_map(function ($k, $v) {
            return [
                $k => array_map(function ($kk, $vv) {
                    return [
                        self::N => $kk,
                        self::V => $vv,
                    ];
                }, array_keys($v), $v),
            ];
        }, array_keys($result->getDefaultConfigs()), $result->getDefaultConfigs());

        return $service->write('config', function ($writer) use ($configList) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Store:etc/config.xsd');
            $writer->setIndentString(self::TAB);
            $writer->write([
                'default' => [
                    'carriers' => $configList,
                ],
            ]);
        });
    }

    private function genSystemXml(Result $result)
    {
        $service = $this->getService();
        $namespace = str_replace('_', '\\', $result->getModule());

        $configList = array_map(function ($key, $entity) use ($namespace) {
            return [
                'group' => [
                    self::A => [
                        'id' => $key,
                        'translate' => 'label',
                        'type' => 'text',
                        'sortOrder' => '99',
                        'showInDefault' => '1',
                        'showInWebsite' => '1',
                        'showInStore' => '1',
                    ],
                    self::V => $this->generateAdminConfig($entity, $namespace),
                ],
            ];
        }, array_keys($result->getCarrierConfigs()), $result->getCarrierConfigs());

        return $service->write('config', function ($writer) use ($configList) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Config:etc/system_file.xsd');
            $writer->setIndentString(self::TAB);
            $writer->write([
                'system' => [
                    'section' => [
                        self::A => [
                            'id' => 'carriers',
                        ],
                        self::V => $configList,
                    ],
                ],
            ]);
        });
    }

    private function generateAdminConfig(array $entity, string $namespace): array
    {
        $generator = new System();

        return [
            'label' => $entity['title'],
            $generator->getEnabled(),
            $generator->getTitle(),
            $generator->getSort(),
            $generator->getTest(),
            $generator->getDebug(),
            $generator->getAuthGroup(),
            $generator->getOptionsGroup(),
            $generator->getPackageGroup($entity['title']),
            $generator->getTaxGroup($namespace),
            $generator->getOrderStatusGroup(),
            $generator->getMarkingGroup(),
        ];
    }
}
