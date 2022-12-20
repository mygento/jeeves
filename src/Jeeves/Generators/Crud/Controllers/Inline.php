<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Inline extends Common
{
    public function genAdminInlineController(
        string $entity,
        string $className,
        string $repository,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = version_compare($phpVersion, '7.4.0', '>=');
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity);

        $json = $class->addProperty('jsonFactory')
            ->setVisibility('private');

        if ($typehint) {
            $namespace->addUse($rootNamespace . '\Controller\Adminhtml\\' . $entity);
            $json->setType('\Magento\Framework\Controller\Result\JsonFactory');
        } else {
            $json->addComment('@var \Magento\Framework\Controller\Result\JsonFactory');
        }
        $namespace->addUse('\Magento\Framework\Exception\NoSuchEntityException');

        $construct = $class->addMethod('__construct')
            ->setBody(
                'parent::__construct($repository, $coreRegistry, $context);' . PHP_EOL . PHP_EOL
                . '$this->jsonFactory = $jsonFactory;'
            );

        $construct->addParameter('jsonFactory')
            ->setTypeHint('\Magento\Framework\Controller\Result\JsonFactory');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Controller\Result\JsonFactory');
            $namespace->addUse($repository);
            $namespace->addUse('\Magento\Framework\Registry');
            $namespace->addUse('\Magento\Backend\App\Action\Context');
        } else {
            $construct
                ->addComment('@param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory')
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
        }

        $execute = $class->addMethod('execute')
            ->addComment('Execute action')
            ->addComment('')
            ->setBody('/** @var \Magento\Framework\Controller\Result\Json $resultJson */' . PHP_EOL
        . '$resultJson = $this->jsonFactory->create();' . PHP_EOL
        . '$error = false;' . PHP_EOL
        . '$messages = [];' . PHP_EOL . PHP_EOL
        . '$postItems = $this->getRequest()->getParam(\'items\', []);' . PHP_EOL
        . 'if (!($this->getRequest()->getParam(\'isAjax\') && count($postItems))) {' . PHP_EOL
        . self::TAB . 'return $resultJson->setData([' . PHP_EOL
        . self::TAB . self::TAB . '\'messages\' => [__(\'Please correct the data sent.\')->render()],' . PHP_EOL
        . self::TAB . self::TAB . '\'error\' => true,' . PHP_EOL
        . self::TAB . ']);' . PHP_EOL
        . '}' . PHP_EOL . PHP_EOL
        . 'foreach (array_keys($postItems) as $id) {' . PHP_EOL
        . self::TAB . 'try {' . PHP_EOL
        . self::TAB . self::TAB . '$entity = $this->repository->getById($id);' . PHP_EOL
        . self::TAB . self::TAB . '$entity->setData(array_merge($entity->getData(), $postItems[$id]));' . PHP_EOL
        . self::TAB . self::TAB . '$this->repository->save($entity);' . PHP_EOL
        . self::TAB . '} catch (NoSuchEntityException $e) {' . PHP_EOL
        . self::TAB . self::TAB . '$messages[] = $id .\' -> \'. __(\'Not found\')->render();' . PHP_EOL
        . self::TAB . self::TAB . '$error = true;' . PHP_EOL
        . self::TAB . self::TAB . 'continue;' . PHP_EOL
        . self::TAB . '} catch (\Exception $e) {' . PHP_EOL
        . self::TAB . self::TAB . '$messages[] = __($e->getMessage());' . PHP_EOL
        . self::TAB . self::TAB . '$error = true;' . PHP_EOL
        . self::TAB . self::TAB . 'continue;' . PHP_EOL
        . self::TAB . '}' . PHP_EOL
        . '}' . PHP_EOL . PHP_EOL

        . 'return $resultJson->setData([' . PHP_EOL
        . self::TAB . '\'messages\' => $messages,' . PHP_EOL
        . self::TAB . '\'error\' => $error' . PHP_EOL
        . ']);' . PHP_EOL);

        if ($typehint) {
            $execute->setReturnType('\Magento\Framework\Controller\ResultInterface');
            $namespace->addUse('\Magento\Framework\Controller\ResultInterface');
        } else {
            $execute->addComment('@return \Magento\Framework\Controller\ResultInterface');
        }

        return $namespace;
    }
}
