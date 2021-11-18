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
        bool $typehint = false
    ): PhpNamespace {
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
                'parent::__construct($repository, $coreRegistry, $context);' . PHP_EOL
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
            ->setBody('/** @var \Magento\Framework\Controller\Result\Json $resultJson */' . PHP_EOL
        . '$resultJson = $this->jsonFactory->create();' . PHP_EOL
        . '$error = false;' . PHP_EOL
        . '$messages = [];' . PHP_EOL . PHP_EOL
        . '$postItems = $this->getRequest()->getParam(\'items\', []);' . PHP_EOL
        . 'if (!($this->getRequest()->getParam(\'isAjax\') && count($postItems))) {' . PHP_EOL
        . '    return $resultJson->setData([' . PHP_EOL
        . '        \'messages\' => [__(\'Please correct the data sent.\')],' . PHP_EOL
        . '        \'error\' => true,' . PHP_EOL
        . '    ]);' . PHP_EOL
        . '}' . PHP_EOL . PHP_EOL
        . 'foreach (array_keys($postItems) as $id) {' . PHP_EOL
        . '    try {' . PHP_EOL
        . '        $entity = $this->repository->getById($id);' . PHP_EOL
        . '        $entity->setData(array_merge($entity->getData(), $postItems[$id]));' . PHP_EOL
        . '        $this->repository->save($entity);' . PHP_EOL
        . '    } catch (NoSuchEntityException $e) {' . PHP_EOL
        . '        $messages[] = $id .\' -> \'. __(\'Not found\');' . PHP_EOL
        . '        $error = true;' . PHP_EOL
        . '        continue;' . PHP_EOL
        . '    } catch (\Exception $e) {' . PHP_EOL
        . '        $messages[] = __($e->getMessage());' . PHP_EOL
        . '        $error = true;' . PHP_EOL
        . '        continue;' . PHP_EOL
        . '    }' . PHP_EOL
        . '}' . PHP_EOL . PHP_EOL

        . 'return $resultJson->setData([' . PHP_EOL
        . '    \'messages\' => $messages,' . PHP_EOL
        . '    \'error\' => $error' . PHP_EOL
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
