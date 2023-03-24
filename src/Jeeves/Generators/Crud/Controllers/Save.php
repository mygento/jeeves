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
        string $primaryKey,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $entityName = $this->getEntityPrintName($entity);
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $namespace->addUse('Magento\Framework\Exception\LocalizedException');
        $namespace->addUse('Magento\Framework\Exception\NoSuchEntityException');
        $class = $namespace->addClass('Save')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        if (!$constructorProp) {
            $persistor = $class->addProperty('dataPersistor')
                ->setVisibility('private');

            $factory = $class->addProperty('entityFactory')
                ->setVisibility('private');

            if ($typehint) {
                $factory->setType($entityClass . 'Factory');
                $persistor->setType('\Magento\Framework\App\Request\DataPersistorInterface');
            } else {
                $persistor->addComment('@var \Magento\Framework\App\Request\DataPersistorInterface');
                $factory->addComment('@var ' . $entityClass . 'Factory');
            }
        }

        $body = 'parent::__construct($repository, $coreRegistry, $context);';
        if (!$constructorProp) {
            $body .= PHP_EOL . PHP_EOL
                . '$this->dataPersistor = $dataPersistor;' . PHP_EOL
                . '$this->entityFactory = $entityFactory;' . PHP_EOL;
        }
        $construct = $class->addMethod('__construct')->setBody($body);

        if ($typehint) {
            $namespace
                ->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity)
                ->addUse('\Magento\Framework\App\Request\DataPersistorInterface')
                ->addUse($entityClass . 'Factory')
                ->addUse($repository)
                ->addUse('\Magento\Framework\Registry')
                ->addUse('\Magento\Backend\App\Action\Context')
                ->addUse('\Magento\Framework\Exception\NoSuchEntityException');
        } else {
            $construct
                ->addComment('@param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor')
                ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
        }

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('dataPersistor')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType('\Magento\Framework\App\Request\DataPersistorInterface');
            $construct
                ->addPromotedParameter('entityFactory')
                ->setReadOnly($readonlyProp)
                ->setPrivate()
                ->setType($entityClass . 'Factory');
        } else {
            $construct
                ->addParameter('dataPersistor')
                ->setType('\Magento\Framework\App\Request\DataPersistorInterface');
            $construct
                ->addParameter('entityFactory')
                ->setType($entityClass . 'Factory');
        }

        $construct->addParameter('repository')->setType($repository);
        $construct->addParameter('coreRegistry')->setType('\Magento\Framework\Registry');
        $construct->addParameter('context')->setType('\Magento\Backend\App\Action\Context');

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
                . '$entityId = (int) $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . '$entity = $this->entityFactory->create();' . PHP_EOL
                . 'if ($entityId) {' . PHP_EOL
                . self::TAB . 'try {' . PHP_EOL
                . self::TAB . self::TAB . '$entity = $this->repository->getById($entityId);' . PHP_EOL
                . self::TAB . '} catch (NoSuchEntityException $e) {' . PHP_EOL
                . self::TAB . self::TAB . 'if (!$entity->getId()) {' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . '$this->messageManager->addErrorMessage(' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . self::TAB . '__(\'This ' . $entityName . ' no longer exists\')->render()' . PHP_EOL
                . self::TAB . self::TAB . self::TAB . ');' . PHP_EOL . PHP_EOL
                . self::TAB . self::TAB . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . self::TAB . self::TAB . '}' . PHP_EOL
                . self::TAB . '}' . PHP_EOL
                . '}' . PHP_EOL
                . 'if (empty($data[\'' . $primaryKey . '\'])) {' . PHP_EOL
                . self::TAB . '$data[\'' . $primaryKey . '\'] = null;' . PHP_EOL
                . '}' . PHP_EOL
                . '$entity->setData($data);' . PHP_EOL
                . 'try {' . PHP_EOL
                . self::TAB . '$this->repository->save($entity);' . PHP_EOL
                . self::TAB . '$this->messageManager->addSuccessMessage(' . PHP_EOL
                . self::TAB . self::TAB . '__(\'You saved the ' . $entityName . '\')->render()' . PHP_EOL
                . self::TAB . ');' . PHP_EOL
                . self::TAB . '$this->dataPersistor->clear(\'' . $this->camelCaseToSnakeCase($shortName) . '\');' . PHP_EOL
                . self::TAB . 'if ($this->getRequest()->getParam(\'back\')) {' . PHP_EOL
                . self::TAB . self::TAB . 'return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $entity->getId()]);' . PHP_EOL
                . self::TAB . '}' . PHP_EOL . PHP_EOL
                . self::TAB . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '} catch (LocalizedException $e) {' . PHP_EOL
                . self::TAB . '$this->messageManager->addErrorMessage($e->getMessage());' . PHP_EOL
                . '} catch (\Exception $e) {' . PHP_EOL
                . self::TAB . '$this->messageManager->addExceptionMessage(' . PHP_EOL
                . self::TAB . self::TAB . '$e, __(\'Something went wrong while saving the ' . $entityName . '\')->render()' . PHP_EOL
                . self::TAB . ');' . PHP_EOL
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
