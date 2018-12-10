<?php

namespace Mygento\SampleModule\Model\ResourceModel\Banner;

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
           \Mygento\SampleModule\Model\Banner::class,
           \Mygento\SampleModule\Model\ResourceModel\Banner::class
        );
    }
}
