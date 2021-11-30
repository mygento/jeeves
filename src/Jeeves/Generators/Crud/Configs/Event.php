<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;

class Event extends Common
{
    public function generateEvents(array $events): string
    {
        $service = $this->getService();

        $eventList = array_map(function ($entity) {
            return [
                [
                    'name' => 'event',
                    'attributes' => [
                        'name' => $entity['event'],
                    ],
                    'value' => array_map(function ($observer) {
                        return [
                            [
                                'name' => 'observer',
                                'attributes' => [
                                    'instance' => $observer['instance'],
                                    'name' => $observer['name'],
                                ],
                            ],
                        ];
                    }, $entity['observer']),
                ],
            ];
        }, $events);

        return $service->write('config', function ($writer) use ($eventList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Event/etc/events.xsd');
            $writer->write($eventList);
        });
    }
}
