<?php

namespace Mygento\SampleModule\Model\ResourceModel\Poster;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Api\Data\PosterInterface;
use Mygento\SampleModule\Model\Poster;
use Mygento\SampleModule\Model\ResourceModel\Poster as PosterResource;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = PosterResource::TABLE_PRIMARY_KEY;

    private MetadataPool $metadataPool;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        MetadataPool $metadataPool,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
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
            Poster::class,
            PosterResource::class
        );
    }

    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(PosterInterface::class);
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

        $entityMetadata = $this->metadataPool->getMetadata(PosterInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $this->getSelect()->join(
            ['store_table' => $this->getMainTable() . '_store'],
            'main_table.' . $linkField . ' = store_table.entity_id',
            []
        )->group('main_table.' . $linkField);

        parent::_renderFiltersBefore();
    }
}
