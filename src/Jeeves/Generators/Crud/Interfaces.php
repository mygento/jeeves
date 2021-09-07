<?php

namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class Interfaces extends Common
{
    public function genModelInterface($className, $rootNamespace, $cacheTag, $fields = self::DEFAULT_FIELDS, $withStore = false)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Api\Data');
        $interface = $namespace->addInterface($className);
        $interface->setExtends('\Magento\Framework\DataObject\IdentityInterface');

        $interface->addConstant('CACHE_TAG', $cacheTag);
        if ($withStore) {
            $fields['store_id'] = [
                'type' => 'store',
            ];
        }

        foreach ($fields as $name => $value) {
            $notNullable = isset($value['nullable']) && $value['nullable'] === false;
            $interface->addConstant(strtoupper($name), strtolower($name))->setPublic();
            $method = $this->snakeCaseToUpperCamelCase($name);
            $interface->addMethod('get' . $method)
                ->addComment('Get ' . str_replace('_', ' ', $name))
                ->addComment('@return ' . $this->convertType($value['type']) . ($notNullable ? '' : '|null'))
                ->setVisibility('public');
            $interface->addMethod('set' . $method)
                ->addComment('Set ' . str_replace('_', ' ', $name))
                ->addComment('@param ' . $this->convertType($value['type']) . ' $' . $this->snakeCaseToCamelCase($name))
                ->addComment('@return $this')
                ->setVisibility('public')
                ->addParameter($this->snakeCaseToCamelCase($name));
        }

        return $namespace;
    }

    public function genModelRepositoryInterface(
        $entity,
        $entInterface,
        $resultInterface,
        $className,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Api');
        $interface = $namespace->addInterface($className);
        $interface->addMethod('save')
            ->addComment('Save ' . $entity)
            ->addComment('@param ' . $entInterface . ' $entity')
            ->addComment('@return ' . $entInterface)
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public')
            ->addParameter('entity')->setTypeHint($entInterface);

        $interface->addMethod('getById')
            ->addComment('Retrieve ' . $entity)
            ->addComment('@param int $entityId')
            ->addComment('@return ' . $entInterface)
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public')
            ->addParameter('entityId');

        $interface->addMethod('getList')
            ->addComment('Retrieve ' . $entity . ' entities matching the specified criteria')
            ->addComment('@param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria')
            ->addComment('@return ' . $resultInterface)
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public')
            ->addParameter('searchCriteria')
            ->setTypeHint('\Magento\Framework\Api\SearchCriteriaInterface');

        $interface->addMethod('delete')
            ->addComment('Delete ' . $entity)
            ->addComment('@param ' . $entInterface . ' $entity')
            ->addComment('@return bool true on success')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public')
            ->addParameter('entity')->setTypeHint($entInterface);

        $interface->addMethod('deleteById')
            ->addComment('Delete ' . $entity)
            ->addComment('@param int $entityId')
            ->addComment('@return bool true on success')
            ->addComment('@throws \Magento\Framework\Exception\NoSuchEntityException')
            ->addComment('@throws \Magento\Framework\Exception\LocalizedException')
            ->setVisibility('public')
            ->addParameter('entityId');

        return $namespace;
    }

    public function genModelSearchInterface($entity, $className, $entInterface, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Api\Data');
        $interface = $namespace->addInterface($className);
        $interface->setExtends('Magento\Framework\Api\SearchResultsInterface');
        $interface->addMethod('getItems')
            ->setVisibility('public')
            ->addComment('Get list of ' . $entity)
            ->addComment('@return ' . $entInterface . '[]');

        $interface->addMethod('setItems')
            ->setVisibility('public')
            ->addComment('Set list of ' . $entity)
            ->addComment('@param ' . $entInterface . '[] $items')
            ->addParameter('items')->setTypeHint('array');

        return $namespace;
    }
}
