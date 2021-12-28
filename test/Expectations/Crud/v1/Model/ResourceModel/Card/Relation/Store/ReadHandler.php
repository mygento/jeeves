<?php

namespace Mygento\SampleModule\Model\ResourceModel\Card\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Mygento\SampleModule\Model\ResourceModel\Card;

class ReadHandler implements ExtensionInterface
{
    private Card $resource;

    public function __construct(Card $resource)
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
