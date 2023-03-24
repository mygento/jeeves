<?php

namespace Mygento\SampleModule\Model\ResourceModel\Card\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Mygento\SampleModule\Api\Data\CardInterface;
use Mygento\SampleModule\Model\ResourceModel\Card;

class ReadHandler implements ExtensionInterface
{
    public function __construct(private readonly Card $resource)
    {
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resource->lookupStoreIds((int) $entity->getId());
            $entity->setData(CardInterface::STORE_ID, $stores);
        }

        return $entity;
    }
}
