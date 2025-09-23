<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class View extends Common
{
    public function genAdminViewController(
        string $entity,
        string $shortName,
        string $repository,
        string $acl,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION,
    ): PhpNamespace {
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $entityName = $this->getEntityPrintName($entity);
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass('Index')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $namespace->addUse('\Magento\Framework\View\Result\PageFactory');
        $namespace->addUse('\Magento\Framework\App\Request\DataPersistorInterface');
        $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        if (!$constructorProp) {
            $result = $class->addProperty('resultPageFactory')
                ->setVisibility('private');
            $pers = $class->addProperty('dataPersistor')
                ->setVisibility('private');

            $result->setType('\Magento\Framework\View\Result\PageFactory');
            $pers->setType('\Magento\Framework\App\Request\DataPersistorInterface');
        }

        $body = 'parent::__construct($repository, $coreRegistry, $context);';
        if (!$constructorProp) {
            $body .= PHP_EOL . PHP_EOL
                . '$this->resultPageFactory = $resultPageFactory;' . PHP_EOL
                . '$this->dataPersistor = $dataPersistor;' . PHP_EOL;
        }
        $construct = $class->addMethod('__construct')
            ->setBody($body);

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('resultPageFactory')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Framework\View\Result\PageFactory');
            $construct
                ->addPromotedParameter('dataPersistor')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Framework\App\Request\DataPersistorInterface');
        } else {
            $construct
                ->addParameter('resultPageFactory')
                ->setType('\Magento\Framework\View\Result\PageFactory');
            $construct
                ->addParameter('dataPersistor')
                ->setType('\Magento\Framework\App\Request\DataPersistorInterface');
        }

        $construct->addParameter('repository')->setType($repository);
        $construct->addParameter('coreRegistry')->setType('\Magento\Framework\Registry');
        $construct->addParameter('context')->setType('\Magento\Backend\App\Action\Context');

        $namespace->addUse($repository);
        $namespace->addUse('\Magento\Framework\Registry');
        $namespace->addUse('\Magento\Backend\App\Action\Context');

        $execute = $class->addMethod('execute')
            ->addComment('Index action')
            ->addComment('')
            ->setBody(' /** @var \Magento\Backend\Model\View\Result\Page $resultPage */' . PHP_EOL
                . '$resultPage = $this->resultPageFactory->create();' . PHP_EOL
                . '$resultPage' . PHP_EOL
                . self::TAB . '->setActiveMenu(\'' . $acl . '\')' . PHP_EOL
                . self::TAB . '->getConfig()' . PHP_EOL
                . self::TAB . '->getTitle()->prepend(__(\'' . $entityName . '\')->render());' . PHP_EOL . PHP_EOL
                . '$this->dataPersistor->clear(\'' . $this->camelCaseToSnakeCase($shortName) . '\');' . PHP_EOL
                . 'return $resultPage;');

        $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
        $namespace->addUse('\Magento\Framework\Controller\ResultInterface');

        return $namespace;
    }
}
