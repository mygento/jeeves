<?php

namespace Mygento\SampleModule\Model\ResourceModel;

class CustomerAddress extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public const TABLE_NAME = 'mygento_sample_custom_table_name';
    public const TABLE_PRIMARY_KEY = 'id';

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }
}
