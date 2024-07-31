<?php

namespace Mygento\SampleModule\Model\ResourceModel\Obsolete;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Model\Obsolete;
use Mygento\SampleModule\Model\ResourceModel\Obsolete as ObsoleteResource;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = ObsoleteResource::TABLE_PRIMARY_KEY;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            Obsolete::class,
            ObsoleteResource::class
        );
    }
}
