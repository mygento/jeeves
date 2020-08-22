<?php

namespace Mygento\SampleModule\Model\ResourceModel\Banner\Relation\Store;

class ReadHandler implements \Magento\Framework\EntityManager\Operation\ExtensionInterface
{
    /** @var \Mygento\SampleModule\Model\ResourceModel\Banner */
    private $resource;

    public function __construct(\Mygento\SampleModule\Model\ResourceModel\Banner $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resource->lookupStoreIds((int) $entity->getId());
            $entity->setData('store_id', $stores);
        }

        return $entity;
    }
}
