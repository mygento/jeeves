<?php

namespace Mygento\Jeeves\Generators\Crud\Interfaces;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Repository extends Common
{
    public function genModelRepositoryInterface(
        string $entInterface,
        string $resultInterface,
        string $className,
        string $print,
        string $rootNamespace,
        bool $hasApi = false,
        bool $typehint = false
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Api');
        $interface = $namespace->addInterface($className);

        if ($hasApi) {
            $interface->addComment('@api');
        }

        if ($typehint) {
            $namespace->addUse('\Magento\Framework\Api\SearchCriteriaInterface');
        }

        $save = $interface->addMethod('save');
        $save->addComment('Save ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi || !$typehint) {
            $save->addComment('@return ' . $entInterface);
        }

        $save->addParameter('entity')->setTypeHint($entInterface);

        if ($typehint) {
            $save->setReturnType($entInterface);
        } else {
            $save->addComment('@param ' . $entInterface . ' $entity');
        }

        $get = $interface->addMethod('getById');
        $get->addComment('Retrieve ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        $getParam = $get->addParameter('entityId');

        if ($hasApi || !$typehint) {
            $get->addComment('@return ' . $entInterface);
        }

        if ($typehint) {
            $get->setReturnType($entInterface);
            $getParam->setType('int');
        } else {
            $get->addComment('@param int $entityId');
        }

        $getList = $interface->addMethod('getList');
        $getList->addComment('Retrieve ' . $print . ' entities matching the specified criteria');

        $getList
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi || !$typehint) {
            $getList->addComment('@return ' . $resultInterface);
        }

        $getList->addParameter('searchCriteria')
            ->setTypeHint('\Magento\Framework\Api\SearchCriteriaInterface');

        if ($typehint) {
            $getList->setReturnType($resultInterface);
        } else {
            $getList
                ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria');
        }

        $del = $interface->addMethod('delete');
        $del
            ->addComment('Delete ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi || !$typehint) {
            $del->addComment('@return bool true on success');
        }

        $del->addParameter('entity')
            ->setTypeHint($entInterface);

        if ($typehint) {
            $del->setReturnType('bool');
        } else {
            $del->addComment('@param ' . $entInterface . ' $entity');
        }

        $delId = $interface->addMethod('deleteById');
        $delId->addComment('Delete ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi || !$typehint) {
            $delId->addComment('@return bool true on success');
        }

        $delParam = $delId->addParameter('entityId');

        if ($typehint) {
            $delId->setReturnType('bool');
            $delParam->setType('int');
        } else {
            $delId->addComment('@param int $entityId');
        }

        return $namespace;
    }
}
