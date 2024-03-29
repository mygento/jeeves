<?php

namespace Mygento\Jeeves\Generators\Crud\Ui;

use Mygento\Jeeves\Generators\Crud\Common;
use Nette\PhpGenerator\PhpNamespace;

class DataProvider extends Common
{
    public function getProvider(
        string $entity,
        string $collection,
        string $collectionFactory,
        string $className,
        string $persistor,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = $this->hasTypes($phpVersion);
        $namespace = new PhpNamespace($rootNamespace . '\Model\\' . ucfirst($entity));
        $namespace->addUse('Magento\Framework\App\Request\DataPersistorInterface');
        $namespace->addUse($collectionFactory);

        if ($typehint) {
            $namespace->addUse('\Magento\Ui\DataProvider\ModifierPoolDataProvider');
            $namespace->addUse($collection);
        }

        $class = $namespace->addClass($className);
        $class->setExtends('\Magento\Ui\DataProvider\ModifierPoolDataProvider');

        $collect = $class->addProperty('collection')
            ->setVisibility('protected');
        $collect->addComment('@var ' . $namespace->simplifyName($collection));

        $persist = $class->addProperty('dataPersistor')
            ->setVisibility('private');
        $loaded = $class->addProperty('loadedData')
            ->setVisibility('private')->setValue([]);

        $namespace->addUse('\Magento\Ui\DataProvider\Modifier\PoolInterface');

        if ($typehint) {
            //$collect->setType($collection);
            $persist->setType('\Magento\Framework\App\Request\DataPersistorInterface');
            $loaded->setType('array');
        } else {
            $persist->addComment('@var DataPersistorInterface');
            $loaded->addComment('@var array');
        }

        $construct = $class->addMethod('__construct')->setVisibility('public');

        if (!$typehint) {
            $construct
                ->addComment('@param \\' . $collectionFactory . ' $collectionFactory')
                ->addComment('@param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor')
                ->addComment('@param string $name')
                ->addComment('@param string $primaryFieldName')
                ->addComment('@param string $requestFieldName')
                ->addComment('@param array $meta')
                ->addComment('@param array $data')
                ->addComment('@param \Magento\Ui\DataProvider\Modifier\PoolInterface|null $pool');
        }

        $construct->addParameter('collectionFactory')->setType($collectionFactory);
        $construct->addParameter('dataPersistor')->setType('\Magento\Framework\App\Request\DataPersistorInterface');
        $construct->addParameter('name')->setType('string');
        $construct->addParameter('primaryFieldName')->setType('string');
        $construct->addParameter('requestFieldName')->setType('string');
        $construct->addParameter('meta')->setType('array')->setDefaultValue([]);
        $construct->addParameter('data')->setType('array')->setDefaultValue([]);
        $construct->addParameter('pool')->setType('\Magento\Ui\DataProvider\Modifier\PoolInterface')->setDefaultValue(null);

        $construct->setBody('parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);' . PHP_EOL . PHP_EOL
            . '$this->collection = $collectionFactory->create();' . PHP_EOL
            . '$this->dataPersistor = $dataPersistor;');

        $getData = $class->addMethod('getData')->setVisibility('public');

        $getData->setBody('if (!empty($this->loadedData)) {' . PHP_EOL
            . '    return $this->loadedData;' . PHP_EOL
            . '}' . PHP_EOL
            . '$items = $this->collection->getItems();' . PHP_EOL
            . 'foreach ($items as $model) {' . PHP_EOL
            . '    $this->loadedData[$model->getId()] = $model->getData();' . PHP_EOL
            . '}' . PHP_EOL
            . '$data = $this->dataPersistor->get(\'' . $persistor . '\');' . PHP_EOL
            . 'if (!empty($data)) {' . PHP_EOL
            . '    $model = $this->collection->getNewEmptyItem();' . PHP_EOL
            . '    $model->setData($data);' . PHP_EOL
            . '    $this->loadedData[$model->getId()] = $model->getData();' . PHP_EOL
            . '    $this->dataPersistor->clear(\'' . $persistor . '\');' . PHP_EOL
            . '}' . PHP_EOL
            . 'return $this->loadedData;');

        if ($typehint) {
            $getData->setReturnType('array');
        } else {
            $getData->addComment('@return array');
        }

        return $namespace;
    }
}
