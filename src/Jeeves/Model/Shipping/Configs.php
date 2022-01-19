<?php

namespace Mygento\Jeeves\Model\Shipping;

use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;
use Sabre\Xml\Service;

class Configs extends Generator
{
    private const TAB = '    ';

    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generate(Result $result)
    {
        $this->writeFile(
            $result->getPath() . '/etc/adminhtml/system.xml',
            $this->genSystemXml()
        );
    }

    private function genSystemXml()
    {
        $service = $this->getService();
        $module= '';
        $entity= '';
        $namespace = '';

        return $service->write('config', function ($writer) use ($module, $entity, $namespace) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Config:etc/system_file.xsd');
            $writer->setIndentString(self::TAB);
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
                                    // $this->getShipping()->getEnabled(),
                                    // $this->getShipping()->getTitle(),
                                    // $this->getShipping()->getSort(),
                                    // $this->getShipping()->getTest(),
                                    // $this->getShipping()->getDebug(),
                                    // $this->getShipping()->getAuthGroup(),
                                    // $this->getShipping()->getOptionsGroup(),
                                    // $this->getShipping()->getPackageGroup($module),
                                    // $this->getShipping()->getTaxGroup($namespace),
                                    // $this->getShipping()->getOrderStatusGroup(),
                                    // $this->getShipping()->getMarkingGroup(),
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    protected function getService(): Service
    {
        $service = new Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }
}