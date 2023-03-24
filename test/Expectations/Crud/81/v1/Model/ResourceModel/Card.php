<?php

namespace Mygento\SampleModule\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Mygento\SampleModule\Api\Data\CardInterface;

class Card extends AbstractDb
{
    public const TABLE_NAME = 'mygento_sample_module_card';
    public const TABLE_PRIMARY_KEY = 'card_id';

    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly MetadataPool $metadataPool,
        Context $context,
        string $connectionName = null,
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(CardInterface::class)->getEntityConnection();
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
     */
    public function lookupStoreIds(int $id): array
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(CardInterface::class);
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
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }
}
