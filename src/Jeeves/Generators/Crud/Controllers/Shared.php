<?php

namespace Mygento\Jeeves\Generators\Crud\Controllers;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Shared extends Common
{
    public function genAdminAbstractController(
        string $className,
        string $acl,
        string $repository,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml');

        if ($typehint) {
            $namespace->addUse('\Magento\Backend\App\Action');
        }

        $class = $namespace->addClass($className)
            ->setAbstract()
            ->setExtends('\Magento\Backend\App\Action');
        $class->addConstant('ADMIN_RESOURCE', $acl)
            ->addComment('Authorization level')
            ->addComment('')
            ->addComment('@see _isAllowed()');

        $reg = $class->addProperty('coreRegistry')
            ->setVisibility('protected');

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Registry');
            $reg->setType('\Magento\Framework\Registry');
        } else {
            $reg->addComment('Core registry');
            $reg->addComment('@var \Magento\Framework\Registry');
        }

        $repo = $class->addProperty('repository')
            ->setVisibility('protected');

        if ($typehint) {
            $repo->setType($repository);
            $namespace->addUse($repository);
        } else {
            $repo->addComment($className . ' repository')
                ->addComment('@var ' . $repository);
        }

        $construct = $class->addMethod('__construct')
            ->setBody('parent::__construct($context);' . PHP_EOL . PHP_EOL
                . '$this->repository = $repository;' . PHP_EOL
                . '$this->coreRegistry = $coreRegistry;');

        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        if (!$typehint) {
            $construct
                ->addComment('@param ' . $repository . ' $repository')
                ->addComment('@param \Magento\Framework\Registry $coreRegistry')
                ->addComment('@param \Magento\Backend\App\Action\Context $context');
        }

        return $namespace;
    }
}
