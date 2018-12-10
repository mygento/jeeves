<?php

namespace Mygento\SampleModule\Model\ResourceModel\CustomerAddress;

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
           \Mygento\SampleModule\Model\CustomerAddress::class,
           \Mygento\SampleModule\Model\ResourceModel\CustomerAddress::class
        );
    }
}
