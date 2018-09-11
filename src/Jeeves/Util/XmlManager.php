<?php
namespace Mygento\Jeeves\Util;

class XmlManager
{
    public function generateAdminRoute($module, $path, $fullname)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($module, $path, $fullname) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:App/etc/routes.xsd');
            $writer->write([
              'name' => 'router',
              'attributes' => [
                'id' => 'admin'
              ],
              'value' => [
                  [
                    'name' => 'route',
                    'attributes' => [
                      'id' => strtolower($module),
                      'frontName' => $path,
                    ],
                    'value' => [
                      'name' => 'module',
                      'attributes' => [
                        'name' => $fullname,
                        'before' => 'Magento_Backend',
                      ],
                    ]
                  ]
              ]
            ]);
        });
    }

    public function generateAdminAcl($fullname, $module, $entity)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($fullname, $module, $entity) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Acl/etc/acl.xsd');
            $writer->write([
                'acl' => [
                    'resources' => [
                        'resource' => [
                            'attributes' => [
                                'id' => 'Magento_Backend::admin'
                            ],
                            'value' => [
                                [
                                    'name' => 'resource',
                                    'attributes' => [
                                        'id' => $fullname . '::' . $entity,
                                        'title' => ucfirst($module) . ' ' . ucfirst($entity),
                                        'translate' => 'title'
                                    ],
                                ],
                                [
                                    'name' => 'resource',
                                    'attributes' => [
                                        'id' => 'Magento_Backend::stores',
                                    ],
                                    'value' => [
                                        'resource' => [
                                            'attributes' => [
                                                'id' => 'Magento_Backend::stores_settings',
                                            ],
                                            'value' => [
                                                'resource' => [
                                                    'attributes' => [
                                                        'id' => 'Magento_Config::config',
                                                    ],
                                                    'value' => [
                                                        'resource' => [
                                                            'attributes' => [
                                                                'id' => $fullname . '::config',
                                                                'title' => str_replace('_', ' ', $fullname),
                                                                'translate' => 'title'
                                                            ],
                                                        ],
                                                    ]
                                                ],
                                            ]
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        });
    }

    private function getService()
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        return $service;
    }
}
