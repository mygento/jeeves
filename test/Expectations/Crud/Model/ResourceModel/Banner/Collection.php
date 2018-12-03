<?php

namespace Mygento\Sample\Model\ResourceModel\Banner;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /** @var string */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
           \Mygento\Sample\Model\Banner::class,
           \Mygento\Sample\Model\ResourceModel\Banner::class
        );
    }
}
