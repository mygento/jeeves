<?php

namespace Mygento\SampleModule\Model\ResourceModel\Banner;

use Mygento\SampleModule\Api\Data\BannerInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'id';

    /** @var \Magento\Framework\EntityManager\MetadataPool */
    private $metadataPool;

    /**
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->metadataPool = $metadataPool;
    }

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Mygento\SampleModule\Model\Banner::class,
            \Mygento\SampleModule\Model\ResourceModel\Banner::class
        );
    }

    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
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

        $entityMetadata = $this->metadataPool->getMetadata(BannerInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $this->getSelect()->join(
            ['store_table' => $this->getMainTable() . '_store'],
            'main_table.' . $linkField . ' = store_table.entity_id',
            []
        )->group('main_table.' . $linkField);

        parent::_renderFiltersBefore();
    }
}
