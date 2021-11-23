<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Layouts as Generators;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Layouts extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateAdminLayouts(Entity $entity)
    {
        $parent = $entity->getModule()->getModuleLowercase() . '_' . $entity->getEntityLowercase();
        $this->genAdminLayoutIndex($entity, $parent);

        if (!$entity->isReadOnly()) {
            $editUiComponent = $parent . '_edit';
            $this->genAdminLayoutEdit($entity, $parent, $editUiComponent);
            $this->genAdminLayoutNew($entity, $parent, $editUiComponent);
        }
    }

    private function genAdminLayoutIndex(Entity $entity, string $parent)
    {
        $generator = new Generators\Index();

        $uiComponent = $parent . '_listing';
        $path = $parent . '_index';
        $this->writeFile(
            $entity->getPath() . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutIndex($uiComponent)
        );
    }

    private function genAdminLayoutEdit(Entity $entity, string $parent, string $editUiComponent)
    {
        $generator = new Generators\Edit();

        $path = $parent . '_edit';
        $this->writeFile(
            $entity->getPath() . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutEdit($editUiComponent)
        );
    }

    private function genAdminLayoutNew(Entity $entity, string $parent, string $editUiComponent)
    {
        $generator = new Generators\Create();
        $path = $parent . '_new';
        $this->writeFile(
            $entity->getPath() . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutNew($editUiComponent)
        );
    }
}
