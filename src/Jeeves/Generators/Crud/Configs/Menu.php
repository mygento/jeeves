<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;
use Mygento\Jeeves\Model;

class Menu extends Common
{
    public function generateAdminMenu(array $entities): string
    {
        $entityList = array_map(
            function ($entity) {
                return $this->getAdminMenuEntity($entity);
            },
            $entities
        );
        $service = $this->getService();

        return $service->write('config', function ($writer) use ($entityList) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:module:Magento_Backend:etc/menu.xsd');
            $writer->setIndentString(self::TAB);
            $writer->write([
                'menu' => $entityList,
            ]);
        });
    }

    private function getAdminMenuEntity(Model\Menu $menu): array
    {
        $result = [
            'name' => 'add',
            'attributes' => [
                'id' => $menu->getId(),
                'title' => $menu->getName(),
                'translate' => 'title',
                'module' => $menu->getCode(),
                'sortOrder' => '90',
                'parent' => $menu->getParent(),
                'resource' => $menu->getResource(),
            ],
        ];
        if ($menu->getAction()) {
            $result['attributes']['action'] = $menu->getAction();
        }

        return $result;
    }
}
