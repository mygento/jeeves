<?php

namespace Mygento\Samplemodule\Model\ResourceModel\Customeraddress;

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
           \Mygento\Samplemodule\Model\Customeraddress::class,
           \Mygento\Samplemodule\Model\ResourceModel\Customeraddress::class
        );
    }
}
