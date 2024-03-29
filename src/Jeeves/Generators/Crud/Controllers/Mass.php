<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Mass extends Common
{
    public function genAdminMassController(
        string $entity,
        string $className,
        string $collection,
        string $repository,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $namespace->addUse('Magento\Framework\Controller\ResultFactory');

        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        if ($typehint) {
            $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
            $namespace->addUse('\Magento\Ui\Component\MassAction\Filter');
            $namespace->addUse($collection);
        }

        if (!$constructorProp) {
            $filter = $class->addProperty('filter')
                ->setVisibility('private');

            $collect = $class->addProperty('collectionFactory')
                ->setVisibility('private');

            if ($typehint) {
                $filter->setType('\Magento\Ui\Component\MassAction\Filter');
                $collect->setType($collection);
            } else {
                $filter->addComment('@var \Magento\Ui\Component\MassAction\Filter');
                $collect->addComment('@var ' . $collection);
            }
        }

        $body = '';
        if (!$constructorProp) {
            $body .= '$this->filter = $filter;' . PHP_EOL
            . '$this->collectionFactory = $collectionFactory;' . PHP_EOL;
        }
        $body .= 'parent::__construct($repository, $coreRegistry, $context);';
        $construct = $class->addMethod('__construct')->setBody($body);

        if ($typehint) {
            $namespace->addUse($repository);
            $namespace->addUse('\Magento\Framework\Registry');
            $namespace->addUse('\Magento\Backend\App\Action\Context');
        } else {
            $construct
                ->addComment('@param ' . $collection . ' $collectionFactory')
                ->addComment('@param \Magento\Ui\Component\MassAction\Filter $filter')
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
        }

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('collectionFactory')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType($collection);
            $construct
                ->addPromotedParameter('filter')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Ui\Component\MassAction\Filter');
        } else {
            $construct
                ->addParameter('collectionFactory')
                ->setType($collection);
            $construct
                ->addParameter('filter')
                ->setType('\Magento\Ui\Component\MassAction\Filter');
        }
        $construct->addParameter('repository')->setType($repository);
        $construct->addParameter('coreRegistry')->setType('\Magento\Framework\Registry');
        $construct->addParameter('context')->setType('\Magento\Backend\App\Action\Context');

        $execute = $class->addMethod('execute')
            ->addComment('Execute action')
            ->addComment('')
            ->setBody('$collection = $this->filter->getCollection($this->collectionFactory->create());' . PHP_EOL
        . '$collectionSize = $collection->getSize();' . PHP_EOL . PHP_EOL

        . 'foreach ($collection as $entity) {' . PHP_EOL
        . self::TAB . '$this->repository->delete($entity);' . PHP_EOL
        . '}' . PHP_EOL

        . '$this->messageManager->addSuccessMessage(' . PHP_EOL
        . self::TAB . '__(\'A total of %1 record(s) have been deleted.\', $collectionSize)->render()' . PHP_EOL
        . ');' . PHP_EOL

        . '/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
        . '$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);' . PHP_EOL
        . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL);

        if ($typehint) {
            $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
            $namespace->addUse('\Magento\Framework\Controller\ResultInterface');
        } else {
            $execute->addComment('@return \Magento\Framework\Controller\ResultInterface');
        }

        return $namespace;
    }
}
