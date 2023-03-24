<?php

namespace Mygento\SampleModule\Model\ResourceModel\Card;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Api\Data\CardInterface;
use Mygento\SampleModule\Model\Card;
use Mygento\SampleModule\Model\ResourceModel\Card as CardResource;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = CardResource::TABLE_PRIMARY_KEY;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private readonly MetadataPool $metadataPool,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            Card::class,
            CardResource::class
        );
    }

    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(CardInterface::class);
        $linkedIds = $this->getColumnValues($entityMetadata->getLinkField());

        if (!count($linkedIds)) {
            return parent::_afterLoad();
        }

        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['entity_store' => $this->getTable($this->getMainTable() . '_store')]
        )->where('entity_store.entity_id IN (?)', $linkedIds);

        $result = $connection->fetchAll($select);
        if (!$result) {
            return parent::_afterLoad();
        }

        $stores = [];
        foreach ($result as $r) {
            $stores[] = $r['store_id'];
        }

        foreach ($this as $item) {
            $item->setData('store_id', $stores);
        }

        return parent::_afterLoad();
    }

    protected function _renderFiltersBefore()
    {
        if (!$this->getFilter('store_id')) {
            parent::_renderFiltersBefore();

            return;
        }

        $entityMetadata = $this->metadataPool->getMetadata(CardInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $this->getSelect()->join(
            ['store_table' => $this->getMainTable() . '_store'],
            'main_table.' . $linkField . ' = store_table.entity_id',
            []
        )->group('main_table.' . $linkField);

        parent::_renderFiltersBefore();
    }
}
