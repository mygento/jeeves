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
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Api');
        $interface = $namespace->addInterface($className);

        if ($hasApi) {
            $interface->addComment('@api');
        }

        $namespace->addUse('\Magento\Framework\Api\SearchCriteriaInterface');

        $save = $interface->addMethod('save');
        $save->addComment('Save ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi) {
            $save->addComment('@return ' . $entInterface);
        }

        $save->addParameter('entity')->setType($entInterface);

        $save->setReturnType($entInterface);

        $get = $interface->addMethod('getById');
        $get->addComment('Retrieve ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        $getParam = $get->addParameter('entityId');

        if ($hasApi) {
            $get->addComment('@return ' . $entInterface);
        }

        $get->setReturnType($entInterface);
        $getParam->setType('int');

        $getList = $interface->addMethod('getList');
        $getList->addComment('Retrieve ' . $print . ' entities matching the specified criteria');

        $getList
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi) {
            $getList->addComment('@return ' . $resultInterface);
        }

        $getList->addParameter('searchCriteria')
            ->setType('\Magento\Framework\Api\SearchCriteriaInterface');

        $getList->setReturnType($resultInterface);

        $del = $interface->addMethod('delete');
        $del
            ->addComment('Delete ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi) {
            $del->addComment('@return bool true on success');
        }

        $del->addParameter('entity')
            ->setType($entInterface);

        $del->setReturnType('bool');

        $delId = $interface->addMethod('deleteById');
        $delId->addComment('Delete ' . $print)
            ->setVisibility('public')
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException');

        if ($hasApi) {
            $delId->addComment('@return bool true on success');
        }

        $delParam = $delId->addParameter('entityId');

        $delId->setReturnType('bool');
        $delParam->setType('int');

        return $namespace;
    }
}
