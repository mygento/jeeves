<?php

namespace Mygento\Jeeves\Generators\Crud\Interfaces;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Repository extends Common
{
    public function genModelRepositoryInterface(
        string $entity,
        string $entInterface,
        string $resultInterface,
        string $className,
        string $rootNamespace,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Api');
        $interface = $namespace->addInterface($className);

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Api\SearchCriteriaInterface');
        }

        $save = $interface->addMethod('save');
        $save->addComment('Save ' . $entity)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');
        $save->addParameter('entity')->setTypeHint($entInterface);

        if ($typehint) {
            $save->setReturnType($entInterface);
        } else {
            $save
                ->addComment('@param ' . $entInterface . ' $entity')
                ->addComment('@return ' . $entInterface);
        }

        $get = $interface->addMethod('getById');
        $get->addComment('Retrieve ' . $entity)
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public');
        $getParam = $get->addParameter('entityId');

        if ($typehint) {
            $get->setReturnType($entInterface);
            $getParam->setType('int');
        } else {
            $get->addComment('@param int $entityId')
                ->addComment('@return ' . $entInterface);
        }

        $getList = $interface->addMethod('getList');
        $getList->addComment('Retrieve ' . $entity . ' entities matching the specified criteria');

        $getList
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->addParameter('searchCriteria')
            ->setTypeHint('\Magento\Framework\Api\SearchCriteriaInterface');
        if ($typehint) {
            $getList->setReturnType($resultInterface);
        } else {
            $getList
                ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria')
                ->addComment('@return ' . $resultInterface);
        }

        $del = $interface->addMethod('delete');
        $del
            ->addComment('Delete ' . $entity)
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public')
            ->addParameter('entity')->setTypeHint($entInterface);
        if ($typehint) {
            $del->setReturnType('bool');
        } else {
            $del->addComment('@param ' . $entInterface . ' $entity')
                ->addComment('@return bool true on success');
        }

        $delId = $interface->addMethod('deleteById');
        $delId->addComment('Delete ' . $entity)
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public');
        $delParam = $delId->addParameter('entityId');

        if ($typehint) {
            $delId->setReturnType('bool');
            $delParam->setType('int');
        } else {
            $delId->addComment('@param int $entityId')
                ->addComment('@return bool true on success');
        }

        return $namespace;
    }
}
