<?php

namespace Mygento\Samplemodule\Model\ResourceModel\Banner;

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
           \Mygento\Samplemodule\Model\Banner::class,
           \Mygento\Samplemodule\Model\ResourceModel\Banner::class
        );
    }
}
