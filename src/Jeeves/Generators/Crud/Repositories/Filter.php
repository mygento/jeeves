<?php

namespace Mygento\Jeeves\Generators\Crud\Repositories;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class Filter extends Common
{
    public function getRepoFilter(string $className, string $rootNamespace, bool $typehint = false): PhpNamespace
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model\SearchCriteria');
        $namespace->addUse('Magento\Framework\Api\Filter');
        $namespace->addUse('Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface');
        $namespace->addUse('Magento\Framework\Data\Collection\AbstractDb');
        $class = $namespace->addClass($className);
        $class->setImplements(['Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface']);

        $apply = $class->addMethod('apply')->setVisibility('public')->addComment('@inheritDoc');
        $apply->addParameter('filter')->setType('Magento\Framework\Api\Filter');
        $apply->addParameter('collection')->setType('Magento\Framework\Data\Collection\AbstractDb');

        if ($typehint) {
            $apply->setReturnType('bool');
        }

        $apply->setBody(
            '$collection->addFilter(' . PHP_EOL
            . self::TAB . '\'store_id\',' . PHP_EOL
            . self::TAB . '[\'in\' => $filter->getValue()]' . PHP_EOL
            . ');' . PHP_EOL . PHP_EOL
            . 'return true;'
        );

        return $namespace;
    }
}
