<?php
namespace Mygento\Jeeves\Util;

class XmlManager
{
    public function generateAdminRoute($module, $path, $fullname)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($module, $path, $fullname) {
            $writer->setIndentString('    ');
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

    public function generateAdminMenu($entity, $path, $fullname, $module)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($entity, $path, $fullname, $module) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Backend:etc/menu.xsd');
            $writer->setIndentString('    ');
            $writer->write([
                'menu' => [
                    [
                        'name'=> 'add',
                        'attributes' => [
                          'id' => $fullname . '::' . $entity,
                          'title' => ucfirst($module) . ' ' . ucfirst($entity),
                          'translate' => 'title',
                          'module' => $fullname,
                          'sortOrder' => '90',
                          'parent' => 'Magento_Backend::stores',
                          'action' => $path . '/' . $entity,
                          'resource' => $fullname . '::' . $entity,
                        ],
                    ]
                ]
            ]);
        });
    }

    public function generateAdminAcl($entity, $fullname, $module)
    {
        $service = $this->getService();
        return $service->write('config', function ($writer) use ($entity, $fullname, $module) {
            $writer->setIndentString('    ');
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
