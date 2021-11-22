<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Controllers  as Generators;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Controllers extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateControllers(Entity $entity)
    {
        $this->genAdminAbstractController($entity);
        $this->genAdminViewController($entity);

        if (!$entity->isReadOnly()) {
            $this->genAdminEditController($entity);
            $this->genAdminSaveController($entity);
            $this->genAdminDeleteController($entity);
            $this->genAdminNewController($entity);
            $this->genAdminInlineController($entity);
            $this->genAdminMassController($entity);
        }
    }

    private function genAdminAbstractController(Entity $entity)
    {
        $generator = new Generators\Shared();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/';
        $fileName = $entity->getEntityName();
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminAbstractController(
                $fileName,
                $entity->getEntityAcl(),
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminViewController(Entity $entity)
    {
        $generator = new Generators\View();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'Index';
        $namePath = '\\' . $entity->getNamespace() . '\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminViewController(
                $entity->getEntityName(),
                $entity->getModuleLowercase() . '_' . $entity->getEntityLowercase(),
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $entity->getEntityAcl(),
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminEditController(Entity $entity)
    {
        $generator = new Generators\Edit();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'Edit';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminEditController(
                $entity->getEntityName(),
                $entity->getModuleLowercase() . '_' . $entity->getEntityLowercase(),
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $namePath . 'Api\\Data\\' . $entity->getEntityName() . 'Interface',
                $entity->getEntityAcl(),
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminSaveController(Entity $entity)
    {
        $generator = new Generators\Save();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'Save';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminSaveController(
                $entity->getEntityName(),
                $entity->getModuleLowercase() . '_' . $entity->getEntityLowercase(),
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $namePath . 'Api\\Data\\' . $entity->getEntityName() . 'Interface',
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminDeleteController(Entity $entity)
    {
        $generator = new Generators\Delete();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'Delete';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminDeleteController(
                $entity->getEntityName(),
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminNewController(Entity $entity)
    {
        $generator = new Generators\Create();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'NewAction';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminNewController(
                $entity->getEntityName(),
                $fileName,
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminInlineController(Entity $entity)
    {
        $generator = new Generators\Inline();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'InlineEdit';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminInlineController(
                $entity->getEntityName(),
                $fileName,
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }

    private function genAdminMassController(Entity $entity)
    {
        $generator = new Generators\Mass();
        $filePath = $entity->getPath() . '/Controller/Adminhtml/' . $entity->getEntityName() . '/';
        $fileName = 'MassDelete';
        $namePath = '\\' . $entity->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminMassController(
                $entity->getEntityName(),
                $fileName,
                $namePath . 'Model\\ResourceModel\\' . $entity->getEntityName() . '\\CollectionFactory',
                $namePath . 'Api\\' . $entity->getEntityName() . 'RepositoryInterface',
                $entity->getNamespace(),
                $entity->hasTypeHint()
            )
        );
    }
}
