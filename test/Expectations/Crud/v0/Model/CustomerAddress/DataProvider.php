<?php

namespace Mygento\SampleModule\Model\CustomerAddress;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Mygento\SampleModule\Model\ResourceModel\CustomerAddress\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /** @var \Mygento\SampleModule\Model\ResourceModel\CustomerAddress\Collection */
    private $collection;

    /** @var DataPersistorInterface */
    private $dataPersistor;

    /** @var array */
    private $loadedData;

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\CustomerAddress\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     * @param \Magento\Ui\DataProvider\Modifier\PoolInterface|null $pool
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);

        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
        }
        $data = $this->dataPersistor->get('sample_module_customeraddress');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('sample_module_customeraddress');
        }

        return $this->loadedData;
    }
}
