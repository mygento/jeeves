<?php

namespace Mygento\SampleModule\Model\ResourceModel\CartItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Model\CartItem;
use Mygento\SampleModule\Model\ResourceModel\CartItem as CartItemResource;

class Collection extends AbstractCollection
{
    protected string $_idFieldName = CartItemResource::TABLE_PRIMARY_KEY;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            CartItem::class,
            CartItemResource::class
        );
    }
}
