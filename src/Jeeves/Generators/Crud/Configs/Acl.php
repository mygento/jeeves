<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;
use Mygento\Jeeves\Model;

class Acl extends Common
{
    public function generateAdminAcls(array $entities, array $configs): string
    {
        $service = $this->getService();
        $configList = array_map(function ($entity) {
            return $this->generateAdminAcl($entity);
        }, $configs);

        $entityResources = array_map(function ($entity) {
            return $this->generateAdminAcl($entity);
        }, array_values($entities));

        return $service->write('config', function ($writer) use ($entityResources, $configList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Acl/etc/acl.xsd');
            $writer->write([
                'acl' => [
                    'resources' => [
                        'resource' => [
                            self::A => [
                                'id' => 'Magento_Backend::admin',
                            ],
                            self::V => [
                                $entityResources,
                                [
                                    self::N => 'resource',
                                    self::A => [
                                        'id' => 'Magento_Backend::stores',
                                    ],
                                    self::V => [
                                        'resource' => [
                                            self::A => [
                                                'id' => 'Magento_Backend::stores_settings',
                                            ],
                                            self::V => [
                                                'resource' => [
                                                    self::A => [
                                                        'id' => 'Magento_Config::config',
                                                    ],
                                                    self::V => $configList,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
    }

    private function generateAdminAcl(Model\Acl $acl): array
    {
        $result = [
            'name' => 'resource',
            'attributes' => [
                'id' => $acl->getId(),
                'title' => $acl->getTitle(),
                'translate' => 'title',
            ],
        ];
        if ($acl->getChildren()) {
            $result['value'] = array_map(
                function ($entity) {
                    return $this->generateAdminAcl($entity);
                },
                $acl->getChildren()
            );
        }

        return $result;
    }
}
