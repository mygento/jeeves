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
        string $phpVersion = PHP_VERSION,
    ): PhpNamespace {
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $entityName = $this->getEntityPrintName($entity);

        if (!$constructorProp) {
            $forward = $class->addProperty('resultForwardFactory')->setVisibility('private');

            $forward->setType('\Magento\Backend\Model\View\Result\ForwardFactory');
        }

        $body = 'parent::__construct($repository, $coreRegistry, $context);';
        if (!$constructorProp) {
            $body .= PHP_EOL . PHP_EOL . '$this->resultForwardFactory = $resultForwardFactory;' . PHP_EOL;
        }
        $construct = $class->addMethod('__construct')
            ->setBody($body);

        $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $namespace->addUse($repository);
        $namespace->addUse('\Magento\Framework\Registry');
        $namespace->addUse('\Magento\Backend\App\Action\Context');
        $namespace->addUse('\Magento\Backend\Model\View\Result\ForwardFactory');

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('resultForwardFactory')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Backend\Model\View\Result\ForwardFactory');
        } else {
            $construct->addParameter('resultForwardFactory')->setType('\Magento\Backend\Model\View\Result\ForwardFactory');
        }

        $construct->addParameter('repository')->setType($repository);
        $construct->addParameter('coreRegistry')->setType('\Magento\Framework\Registry');
        $construct->addParameter('context')->setType('\Magento\Backend\App\Action\Context');

        $execute = $class->addMethod('execute')
            ->addComment('Create new ' . $entityName)
            ->addComment('')
            ->setBody('/** @var \Magento\Framework\Controller\Result\Forward $resultForward */' . PHP_EOL
                . '$resultForward = $this->resultForwardFactory->create();' . PHP_EOL
                . 'return $resultForward->forward(\'edit\');');

        $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
        $namespace->addUse('\Magento\Framework\Controller\ResultInterface');

        return $namespace;
    }
}
