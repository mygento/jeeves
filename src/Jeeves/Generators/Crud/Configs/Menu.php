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
            self::N => 'add',
            self::A => [
                'id' => $menu->getId(),
                'title' => $menu->getName(),
                'translate' => 'title',
                'module' => $menu->getCode(),
                'sortOrder' => '90',
                'parent' => $menu->getParent(),
            ],
        ];
        if ($menu->getAction()) {
            $result[self::A]['action'] = $menu->getAction();
        }
        $result[self::A]['resource'] = $menu->getResource();

        return $result;
    }
}
