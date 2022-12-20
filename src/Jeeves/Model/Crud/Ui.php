<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Ui as Generators;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Ui extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateAdminUI(Entity $entity)
    {
        $this->genAdminUiDataProvider($entity);
        $this->genAdminUiListing($entity);

        if (!$entity->isReadOnly()) {
            $this->genAdminUIActions($entity);
            $this->genAdminUiEdit($entity);
        }
    }

    public function generateGridCollection(Entity $entity)
    {
        $generator = new Generators\Grid();
        $filePath = $entity->getPath() . '/Model/ResourceModel/' . $entity->getEntityName() . '/Grid/';
        $fileName = 'Collection';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->generateGridCollection(
                $entity->getEntityName(),
                $fileName,
                $entity->getNamespace() . '\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Collection',
                $entity->getNamespace(),
                $entity->withStore(),
                $entity->getPhpVersion()
            )
        );
    }

    private function genAdminUIActions(Entity $entity)
    {
        $generator = new Generators\Actions();
        $filePath = $entity->getPath() . '/Ui/Component/Listing/';
        $fileName = $entity->getEntityName() . 'Actions';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
                $generator->getActions(
                    $entity->getModule()->getRouteName(),
                    $entity->getEntityLowercase(),
                    $entity->getEntityName() . 'Actions',
                    $entity->getPrimaryKey(),
                    $entity->getNamespace(),
                    $entity->getPhpVersion()
                )
        );
    }

    private function genAdminUiDataProvider(Entity $entity)
    {
        $generator = new Generators\DataProvider();
        $filePath = $entity->getPath() . '/Model/' . $entity->getEntityName() . '/';
        $fileName = 'DataProvider';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->getProvider(
                $entity->getName(),
                '\\' . $entity->getNamespace() . '\Model\\ResourceModel\\' . $entity->getEntityName() . '\\Collection',
                $entity->getNamespace() . '\Model\\ResourceModel\\' . $entity->getEntityName() . '\\CollectionFactory',
                $fileName,
                $entity->getModule()->getModuleLowercase() . '_' . $entity->getEntityLowercase(),
                $entity->getNamespace(),
                $entity->getPhpVersion()
            )
        );
    }

    private function genAdminUiListing(Entity $entity)
    {
        $generator = new Generators\Listing();
        $parent = $entity->getModule()->getModuleLowercase() . '_' . $entity->getEntityLowercase();
        $url = $entity->getModule()->getModuleLowercase() . '/' . $entity->getEntityLowercase();

        $uiComponent = $parent . '_listing';
        $common = $parent . '_listing' . '.' . $parent . '_listing.';
        $this->writeFile(
            $entity->getPath() . '/view/adminhtml/ui_component/' . $uiComponent . '.xml',
            $generator->generateAdminUiIndex(
                $uiComponent,
                $uiComponent . '_data_source',
                $parent . '_columns',
                'Add New ' . $this->getConverter()->getEntityPrintName($entity->getName()),
                $entity->getEntityAcl(),
                $entity->getNamespace() . '\Ui\Component\Listing\\' . $entity->getEntityName() . 'Actions',
                $url . '/inlineEdit',
                $url . '/massDelete',
                $common . $parent . '_columns.ids',
                $common . $parent . '_columns_editor',
                $entity->getPrimaryKey(),
                $entity->getColumns(),
                $entity->isReadOnly(),
                $entity->withStore()
            )
        );
    }

    private function genAdminUiEdit(Entity $entity)
    {
        $generator = new Generators\Edit();
        $parent = $entity->getModule()->getModuleLowercase() . '_' . $entity->getEntityLowercase();
        $url = $entity->getModule()->getModuleLowercase() . '/' . $entity->getEntityLowercase();

        $uiComponent = $parent . '_edit';
        $dataSource = $uiComponent . '_data_source';
        $provider = $entity->getNamespace() . '\Model\\' . $entity->getEntityName() . '\DataProvider';
        $this->writeFile(
            $entity->getPath() . '/view/adminhtml/ui_component/' . $uiComponent . '.xml',
            $generator->generateAdminUiForm(
                $uiComponent,
                $dataSource,
                $url . '/save',
                $provider,
                $entity->getEntityName(),
                $entity->getPrimaryKey(),
                $entity->getColumns(),
                $entity->withStore()
            )
        );
    }
}
