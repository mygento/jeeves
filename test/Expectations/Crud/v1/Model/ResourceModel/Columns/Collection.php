<?php

namespace Mygento\SampleModule\Model\ResourceModel\Columns;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Model\Columns;
use Mygento\SampleModule\Model\ResourceModel\Columns as ColumnsResource;

class Collection extends AbstractCollection
{
    protected string $_idFieldName = ColumnsResource::TABLE_PRIMARY_KEY;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            Columns::class,
            ColumnsResource::class
        );
    }
}
