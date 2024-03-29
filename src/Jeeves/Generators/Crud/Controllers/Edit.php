<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Edit extends Common
{
    public function genAdminEditController(
        string $entity,
        string $shortName,
        string $repository,
        string $entityClass,
        string $acl,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $entityName = $this->getEntityPrintName($entity);

        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass('Edit')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        if ($typehint) {
            $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
            $namespace->addUse('\Magento\Framework\View\Result\PageFactory');
            $namespace->addUse($entityClass . 'Factory');
        }

        if (!$constructorProp) {
            $factory = $class->addProperty('entityFactory')
                ->setVisibility('private');

            $result = $class->addProperty('resultPageFactory')
                ->setVisibility('private');

            if ($typehint) {
                $factory->setType($entityClass . 'Factory');
                $result->setType('\Magento\Framework\View\Result\PageFactory');
            } else {
                $factory->addComment('@var ' . $entityClass . 'Factory');
                $result->addComment('@var \Magento\Framework\View\Result\PageFactory');
            }
        }

        $body = 'parent::__construct($repository, $coreRegistry, $context);';
        if (!$constructorProp) {
            $body .= PHP_EOL . PHP_EOL
                . '$this->entityFactory = $entityFactory;' . PHP_EOL
                . '$this->resultPageFactory = $resultPageFactory;' . PHP_EOL;
        }
        $construct = $class->addMethod('__construct')->setBody($body);

        if ($typehint) {
            $namespace
                ->addUse($repository)
                ->addUse('\Magento\Framework\Registry')
                ->addUse('\Magento\Backend\App\Action\Context');
        } else {
            $construct
                ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
                ->addComment('@param \Magento\Framework\View\Result\PageFactory $resultPageFactory')
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
        }

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('entityFactory')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType($entityClass . 'Factory');

            $construct
                ->addPromotedParameter('resultPageFactory')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Framework\View\Result\PageFactory');
        } else {
            $construct
                ->addParameter('entityFactory')
                ->setType($entityClass . 'Factory');
            $construct
                ->addParameter('resultPageFactory')
                ->setType('\Magento\Framework\View\Result\PageFactory');
        }
        $construct->addParameter('repository')->setType($repository);
        $construct->addParameter('coreRegistry')->setType('\Magento\Framework\Registry');
        $construct->addParameter('context')->setType('\Magento\Backend\App\Action\Context');

        $execute = $class->addMethod('execute')
            ->addComment('Edit ' . $entityName . ' action')
            ->addComment('')
            ->setBody('$entityId = (int) $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . '$entity = $this->entityFactory->create();' . PHP_EOL
                . 'if ($entityId) {' . PHP_EOL
                . self::TAB . 'try {' . PHP_EOL
                . self::TAB . self::TAB . '$entity = $this->repository->getById($entityId);' . PHP_EOL
                . self::TAB . '} catch (NoSuchEntityException $e) {' . PHP_EOL
                . self::TAB . self::TAB . '$this->messageManager->addErrorMessage(' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . '__(\'This ' . $entityName . ' no longer exists\')->render()' . PHP_EOL
                . self::TAB . self::TAB . ');' . PHP_EOL
                . self::TAB . self::TAB . '/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
                . self::TAB . self::TAB . '$resultRedirect = $this->resultRedirectFactory->create();' . PHP_EOL . PHP_EOL
                . self::TAB . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . self::TAB . '}' . PHP_EOL
                . '}' . PHP_EOL
                . '$this->coreRegistry->register(\'' . $this->camelCaseToSnakeCase($shortName) . '\', $entity);' . PHP_EOL . PHP_EOL
                . '/** @var \Magento\Backend\Model\View\Result\Page $resultPage */' . PHP_EOL
                . '$resultPage = $this->resultPageFactory->create();' . PHP_EOL
                . '$resultPage->setActiveMenu(\'' . $acl . '\');' . PHP_EOL
                . '$resultPage->addBreadcrumb(' . PHP_EOL
                . self::TAB . '$entityId ? __(\'Edit ' . $entityName . '\')->render() : __(\'New ' . $entityName . '\')->render(),' . PHP_EOL
                . self::TAB . '$entityId ? __(\'Edit ' . $entityName . '\')->render() : __(\'New ' . $entityName . '\')->render()' . PHP_EOL
                . ');' . PHP_EOL
                . '$resultPage->getConfig()->getTitle()->prepend(__(\'' . $entityName . '\')->render());' . PHP_EOL
                . '$resultPage->getConfig()->getTitle()->prepend(' . PHP_EOL
                . self::TAB . '$entityId ? $entity->getTitle() : __(\'New ' . $entityName . '\')->render()' . PHP_EOL
                . ');' . PHP_EOL . PHP_EOL
                . 'return $resultPage;');
        $namespace->addUse('\Magento\Framework\Exception\NoSuchEntityException');

        if ($typehint) {
            $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
            $namespace->addUse('\Magento\Framework\Controller\ResultInterface');
        } else {
            $execute->addComment('@return \Magento\Framework\Controller\ResultInterface');
        }

        return $namespace;
    }
}
