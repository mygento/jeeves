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
        string $phpVersion = PHP_VERSION,
    ): PhpNamespace {
        $constructorProp = $this->hasConstructorProp($phpVersion);
        $readonlyProp = $this->hasReadOnlyProp($phpVersion);

        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml');

        $namespace->addUse('\Magento\Backend\App\Action');

        $class = $namespace->addClass($className)
            ->setAbstract()
            ->setExtends('\Magento\Backend\App\Action');
        $class->addConstant('ADMIN_RESOURCE', $acl)
            ->addComment('Authorization level')
            ->addComment('')
            ->addComment('@see _isAllowed()');

        $namespace->addUse('\Magento\Framework\Registry');
        $namespace->addUse($repository);

        if (!$constructorProp) {
            $reg = $class->addProperty('coreRegistry')
                ->setVisibility('protected');

            $reg->setType('\Magento\Framework\Registry');

            $repo = $class->addProperty('repository')
                ->setVisibility('protected');

            $repo->setType($repository);
        }

        $body = 'parent::__construct($context);';
        if (!$constructorProp) {
            $body .= PHP_EOL . PHP_EOL
                . '$this->repository = $repository;' . PHP_EOL
                . '$this->coreRegistry = $coreRegistry;';
        }

        $construct = $class->addMethod('__construct')
            ->setBody($body);

        if ($constructorProp) {
            $construct
                ->addPromotedParameter('repository')
                ->setReadOnly($readonlyProp)
                ->setProtected()
                ->setType($repository);
            $construct
                ->addPromotedParameter('coreRegistry')
                ->setReadOnly($readonlyProp)
                ->setProtected()
                ->setType('\Magento\Framework\Registry');
        } else {
            $construct->addParameter('repository')->setType($repository);
            $construct->addParameter('coreRegistry')->setType('\Magento\Framework\Registry');
        }
        $construct->addParameter('context')->setType('\Magento\Backend\App\Action\Context');

        return $namespace;
    }
}
