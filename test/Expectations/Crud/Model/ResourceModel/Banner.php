<?php

namespace Mygento\SampleModule\Model\ResourceModel;

class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mygento_sample_module_banner', 'id');
    }
}
