<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Save extends Common
{
    public function genAdminSaveController(
        string $entity,
        string $shortName,
        string $repository,
        string $entityClass,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
        $entityName = $this->getEntityPrintName($entity);
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $namespace->addUse('Magento\Framework\Exception\LocalizedException');
        $namespace->addUse('Magento\Framework\Exception\NoSuchEntityException');
        $class = $namespace->addClass('Save')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $persistor = $class->addProperty('dataPersistor')
            ->setVisibility('private');

        $factory = $class->addProperty('entityFactory')
            ->setVisibility('private');

        if ($typehint) {
            $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
            $factory->setType($entityClass . 'Factory');
            $persistor->setType('\Magento\Framework\App\Request\DataPersistorInterface');
        } else {
            $persistor->addComment('@var \Magento\Framework\App\Request\DataPersistorInterface');
            $factory->addComment('@var ' . $entityClass . 'Factory');
        }

        $construct = $class->addMethod('__construct')
            ->setBody(
                'parent::__construct($repository, $coreRegistry, $context);' . PHP_EOL . PHP_EOL
            . '$this->dataPersistor = $dataPersistor;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            );

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\App\Request\DataPersistorInterface');
            $namespace->addUse($entityClass . 'Factory');
            $namespace->addUse($repository);
            $namespace->addUse('\Magento\Framework\Registry');
            $namespace->addUse('\Magento\Backend\App\Action\Context');
            $namespace->addUse('\Magento\Framework\Exception\NoSuchEntityException');
        } else {
            $construct
                ->addComment('@param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor')
                ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
        }

        $construct->addParameter('dataPersistor')
            ->setTypeHint('\Magento\Framework\App\Request\DataPersistorInterface');
        $construct->addParameter('entityFactory')->setTypeHint($entityClass . 'Factory');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $execute = $class->addMethod('execute')
            ->addComment('Save ' . $entityName . ' action')
            ->addComment('')
            ->addComment('@SuppressWarnings(PHPMD.CouplingBetweenObjects)')
            ->addComment('@SuppressWarnings(PHPMD.CyclomaticComplexity)')
            ->setBody('/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
                . '$resultRedirect = $this->resultRedirectFactory->create();' . PHP_EOL
                . '$data = $this->getRequest()->getPostValue();' . PHP_EOL
                . 'if (!$data) {' . PHP_EOL
                . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '}' . PHP_EOL
                . '$entityId = $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . '$entity = $this->entityFactory->create();' . PHP_EOL
                . 'if ($entityId) {' . PHP_EOL
                . self::TAB . 'try {' . PHP_EOL
                . self::TAB . self::TAB . '$entity = $this->repository->getById($entityId);' . PHP_EOL
                . self::TAB . '} catch (NoSuchEntityException $e) {' . PHP_EOL
                . self::TAB . self::TAB . 'if (!$entity->getId() && $entityId) {' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . '$this' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . self::TAB . '->messageManager' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . self::TAB . '->addErrorMessage(__(\'This ' . $entityName . ' no longer exists\'));' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . self::TAB . self::TAB . '}' . PHP_EOL
                . self::TAB . '}' . PHP_EOL
                . '}' . PHP_EOL
                . 'if (empty($data[\'id\'])) {' . PHP_EOL
                . self::TAB . '$data[\'id\'] = null;' . PHP_EOL
                . '}' . PHP_EOL
                . '$entity->setData($data);' . PHP_EOL
                . 'try {' . PHP_EOL
                . self::TAB . '$this->repository->save($entity);' . PHP_EOL
                . self::TAB . '$this->messageManager->addSuccessMessage(__(\'You saved the ' . $entityName . '\'));' . PHP_EOL
                . self::TAB . '$this->dataPersistor->clear(\'' . $this->camelCaseToSnakeCase($shortName) . '\');' . PHP_EOL
                . self::TAB . 'if ($this->getRequest()->getParam(\'back\')) {' . PHP_EOL
                . self::TAB . self::TAB . 'return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $entity->getId()]);' . PHP_EOL
                . self::TAB . '}' . PHP_EOL
                . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '} catch (LocalizedException $e) {' . PHP_EOL
                . self::TAB . '$this->messageManager->addErrorMessage($e->getMessage());' . PHP_EOL
                . '} catch (\Exception $e) {' . PHP_EOL
                . self::TAB . '$this->messageManager->addExceptionMessage($e, __(\'Something went wrong while saving the ' . $entityName . '\'));' . PHP_EOL
                . '}' . PHP_EOL
                . '$this->dataPersistor->set(\'' . $this->camelCaseToSnakeCase($shortName) . '\', $data);' . PHP_EOL . PHP_EOL
                . 'return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $this->getRequest()->getParam(\'id\')]);');

        if ($typehint) {
            $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
            $namespace->addUse('\Magento\Framework\Controller\ResultInterface');
        } else {
            $execute->addComment('');
            $execute->addComment('@return \Magento\Framework\Controller\ResultInterface');
        }

        return $namespace;
    }
}
