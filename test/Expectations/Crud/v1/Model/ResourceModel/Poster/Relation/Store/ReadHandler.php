<?php

namespace Mygento\SampleModule\Model\ResourceModel\Poster\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Mygento\SampleModule\Model\ResourceModel\Poster;

class ReadHandler implements ExtensionInterface
{
    private Poster $resource;

    public function __construct(Poster $resource)
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
