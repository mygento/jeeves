<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;
use Mygento\Jeeves\Model\Api;

class WebApi extends Common
{
    private const VERSION = '/V1/';

    public function generateWebAPI(array $entities): string
    {
        $service = $this->getService();

        $entityList = array_map(function (Api $entity) {
            return [
                self::N => 'route',
                self::A => [
                    'url' => self::VERSION . $entity->getUrl(),
                    'method' => $entity->getMethod(),
                ],
                self::V => [
                    [
                        self::N => 'service',
                        self::A => [
                            'class' => $entity->getClass(),
                            'method' => $entity->getClassMethod(),
                        ],
                    ],
                    [
                        self::N => 'resources',
                        self::V => [
                            [
                                self::N => 'resource',
                                self::A => [
                                    'ref' => $entity->getResource(),
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }, $entities);

        return $service->write('routes', function ($writer) use ($entityList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute(
                'xsi:noNamespaceSchemaLocation',
                'urn:magento:module:Magento_Webapi:etc/webapi.xsd'
            );
            $writer->write($entityList);
        });
    }
}
