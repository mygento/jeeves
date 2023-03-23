<?php

namespace Mygento\SampleModule\Model\ResourceModel\Banner\Relation\Store;

use Magento\Framework\EntityManager\MetadataPool;
use Mygento\SampleModule\Api\Data\BannerInterface;
use Mygento\SampleModule\Model\ResourceModel\Banner;

class SaveHandler implements \Magento\Framework\EntityManager\Operation\ExtensionInterface
{
    /** @var \Mygento\SampleModule\Model\ResourceModel\Banner */
    private $resource;

    /** @var \Magento\Framework\EntityManager\MetadataPool */
    private $metadataPool;

    public function __construct(Banner $resource, MetadataPool $metadataPool)
    {
        $this->resource = $resource;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $connection = $entityMetadata->getEntityConnection();
        $oldStores = $this->resource->lookupStoreIds((int) $entity->getId());
        $newStores = (array) $entity->getStoreId();
        $table = $this->resource->getTable($entityMetadata->getEntityTable() . '_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'entity_id = ?' => (int) $entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'entity_id' => (int) $entity->getData($linkField),
                    'store_id' => (int) $storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
