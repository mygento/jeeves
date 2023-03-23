<?php

namespace Mygento\SampleModule\Model\ResourceModel\Obsolete;

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
            \Mygento\SampleModule\Model\Obsolete::class,
            \Mygento\SampleModule\Model\ResourceModel\Obsolete::class
        );
    }
}
