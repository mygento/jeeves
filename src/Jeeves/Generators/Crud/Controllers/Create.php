<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Create extends Common
{
    public function genAdminNewController(
        string $entity,
        string $className,
        string $repository,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $entityName = $this->getEntityPrintName($entity);

        $forward = $class->addProperty('resultForwardFactory')
            ->setVisibility('private');

        $construct = $class->addMethod('__construct')
            ->setBody(
                'parent::__construct($repository, $coreRegistry, $context);' . PHP_EOL . PHP_EOL .
                '$this->resultForwardFactory = $resultForwardFactory;' . PHP_EOL
            );

        if ($typehint) {
            $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
            $namespace->addUse($repository);
            $namespace->addUse('\Magento\Framework\Registry');
            $namespace->addUse('\Magento\Backend\App\Action\Context');
            $namespace->addUse('\Magento\Backend\Model\View\Result\ForwardFactory');
            $forward->setType('\Magento\Backend\Model\View\Result\ForwardFactory');
        } else {
            $construct
                ->addComment('@param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory')
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
            $forward->addComment('@var \Magento\Backend\Model\View\Result\ForwardFactory');
        }

        $construct->addParameter('resultForwardFactory')
            ->setTypeHint('\Magento\Backend\Model\View\Result\ForwardFactory');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $execute = $class->addMethod('execute')
            ->addComment('Create new ' . $entityName)
            ->addComment('')
            ->setBody('/** @var \Magento\Framework\Controller\Result\Forward $resultForward */' . PHP_EOL
                . '$resultForward = $this->resultForwardFactory->create();' . PHP_EOL
                . 'return $resultForward->forward(\'edit\');');

        if ($typehint) {
            $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
            $namespace->addUse('\Magento\Framework\Controller\ResultInterface');
        } else {
            $execute->addComment('@return \Magento\Framework\Controller\ResultInterface');
        }

        return $namespace;
    }
}
