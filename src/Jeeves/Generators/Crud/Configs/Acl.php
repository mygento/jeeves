<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;
use Mygento\Jeeves\Model;

class Acl extends Common
{
    public function generateAdminAcl(Model\Acl $acl): array
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

    public function generateAdminAcls(array $entities): string
    {
        $service = $this->getService();
        $entityList = ['xxx'];

        $entityResources = array_map(function ($entity) {
            return $this->generateAdminAcl($entity);
        }, array_values($entities));

        return $service->write('config', function ($writer) use ($entityResources, $entityList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Acl/etc/acl.xsd');
            $writer->write([
                'acl' => [
                    'resources' => [
                        'resource' => [
                            'attributes' => [
                                'id' => 'Magento_Backend::admin',
                            ],
                            'value' => [
                                $entityResources,
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
                                                    'value' => $entityList,
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
}
