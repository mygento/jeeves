<?php

namespace Mygento\SampleModule\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\BannerInterface;

class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /** @var \Magento\Framework\EntityManager\EntityManager */
    private $entityManager;

    /** @var \Magento\Framework\EntityManager\MetadataPool */
    private $metadataPool;

    /**
     * @param \Magento\Framework\EntityManager\EntityManager $entityManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\EntityManager\EntityManager $entityManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(BannerInterface::class)->getEntityConnection();
    }

    /**
     * @inheritDoc
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        return $this->entityManager->load($object, $value);
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);

        return $this;
    }

    /**
     * Find store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['es' => $this->getMainTable() . '_store'], 'store_id')
            ->join(
                ['e' => $this->getMainTable()],
                'es.entity_id = e.' . $linkField,
                []
            )
            ->where('e.' . $entityMetadata->getIdentifierField() . ' = :entity_id');

        return $connection->fetchCol($select, ['entity_id' => (int) $id]);
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mygento_sample_module_banner', 'id');
    }
}
