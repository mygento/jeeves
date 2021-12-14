<?php

namespace Mygento\SampleModule\Model\ResourceModel\Poster\Relation\Store;

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Mygento\SampleModule\Api\Data\PosterInterface;
use Mygento\SampleModule\Model\ResourceModel\Poster;

class SaveHandler implements ExtensionInterface
{
    private Poster $resource;

    private MetadataPool $metadataPool;

    public function __construct(Poster $resource, MetadataPool $metadataPool)
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
        $entityMetadata = $this->metadataPool->getMetadata(PosterInterface::class);
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
