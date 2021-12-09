<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Delete extends Common
{
    public function genAdminDeleteController(
        string $entity,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
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
                . '$entityId = $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . 'if (!$entityId) {' . PHP_EOL
                . '    $this->messageManager->addErrorMessage(__(\'We can not find a ' . $entityName . ' to delete.\'));' . PHP_EOL
                . '     return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '}' . PHP_EOL
                . 'try {' . PHP_EOL
                . '    $this->repository->deleteById($entityId);' . PHP_EOL
                . '    $this->messageManager->addSuccessMessage(__(\'You deleted the ' . $entityName . '\'));' . PHP_EOL
                . '    return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '} catch (\Exception $e) {' . PHP_EOL
                . '    $this->messageManager->addErrorMessage($e->getMessage());' . PHP_EOL
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
