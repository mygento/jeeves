<?php

namespace Mygento\SampleModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CartItem extends AbstractDb
{
    public const TABLE_NAME = 'mygento_sample_custom_cart_item';
    public const TABLE_PRIMARY_KEY = 'cart_id';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }
}
