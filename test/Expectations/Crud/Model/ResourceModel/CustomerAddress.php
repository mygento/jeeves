<?php

namespace Mygento\SampleModule\Model\ResourceModel;

class CustomerAddress extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mygento_sample_custom_table_name', 'id');
    }
}
