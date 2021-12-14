<?php

namespace Mygento\Jeeves\Util;

class XmlManager
{
    public const A = 'attributes';
    public const N = 'name';
    public const V = 'value';

    private $shipping;

    public function generateShippingSystem($module, $entity, $namespace)
    {
        $service = $this->getService();

        return $service->write('config', function ($writer) use ($module, $entity, $namespace) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Config:etc/system_file.xsd');
            $writer->setIndentString('    ');
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
                                    $this->getShipping()->getEnabled(),
                                    $this->getShipping()->getTitle(),
                                    $this->getShipping()->getSort(),
                                    $this->getShipping()->getTest(),
                                    $this->getShipping()->getDebug(),
                                    $this->getShipping()->getAuthGroup(),
                                    $this->getShipping()->getOptionsGroup(),
                                    $this->getShipping()->getPackageGroup($module),
                                    $this->getShipping()->getTaxGroup($namespace),
                                    $this->getShipping()->getOrderStatusGroup(),
                                    $this->getShipping()->getMarkingGroup(),
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    private function getService()
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }

    private function getShipping()
    {
        if (!$this->shipping) {
            $this->shipping = new Xml\Shipping();
        }

        return $this->shipping;
    }
}
