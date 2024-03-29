<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Delete extends Common
{
    public function genAdminDeleteController(
        string $entity,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $class = $namespace->addClass('Delete')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $entityName = $this->getEntityPrintName($entity);

        if ($typehint) {
            $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        }

        $execute = $class->addMethod('execute')
            ->addComment('Delete ' . $entityName . ' action')
            ->addComment('')
            ->setBody('/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
                . '$resultRedirect = $this->resultRedirectFactory->create();' . PHP_EOL
                . '$entityId = (int) $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . 'if (!$entityId) {' . PHP_EOL
                . self::TAB . '$this->messageManager->addErrorMessage(' . PHP_EOL
                . self::TAB . self::TAB . '__(\'We can not find a ' . $entityName . ' to delete.\')->render()' . PHP_EOL
                . self::TAB . ');' . PHP_EOL
                . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '}' . PHP_EOL
                . 'try {' . PHP_EOL
                . self::TAB . '$this->repository->deleteById($entityId);' . PHP_EOL
                . self::TAB . '$this->messageManager->addSuccessMessage(' . PHP_EOL
                . self::TAB . self::TAB . '__(\'You deleted the ' . $entityName . '\')->render()' . PHP_EOL
                . self::TAB . ');' . PHP_EOL . PHP_EOL
                . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '} catch (\Exception $e) {' . PHP_EOL
                . self::TAB . '$this->messageManager->addErrorMessage($e->getMessage());' . PHP_EOL
                . '}' . PHP_EOL . PHP_EOL
                . 'return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $entityId]);');

        if ($typehint) {
            $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
            $namespace->addUse('\Magento\Framework\Controller\ResultInterface');
        } else {
            $execute->addComment('@return \Magento\Framework\Controller\ResultInterface');
        }

        return $namespace;
    }
}
