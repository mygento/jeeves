<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Model\Generator;

class Event extends Generator
{
    public function generateEvents(Entity $entity): array
    {
        $events = [];
        if ($entity->withStore()) {
            $event = $entity->getEntityLowercase();
            $eventName = implode('_', [
                'legacy',
                $entity->getEventName($entity->getName()),
            ]);
            $events[] = [
                'event' => $event . '_save_before',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'before_save',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\BeforeEntitySave',
                ]],
            ];
            $events[] = [
                'event' => $event . '_save_after',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'after_save',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\AfterEntitySave',
                ]],
            ];
            $events[] = [
                'event' => $event . '_delete_before',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'before_delete',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\BeforeEntityDelete',
                ]],
            ];
            $events[] = [
                'event' => $event . '_delete_after',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'after_delete',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\AfterEntityDelete',
                ]],
            ];
        }

        return $events;
    }
}
