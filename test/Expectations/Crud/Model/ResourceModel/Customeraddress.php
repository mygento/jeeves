<?php

namespace Mygento\Sample\Model\ResourceModel;

class Customeraddress extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mygento_sample_customeraddress', 'id');
    }
}
