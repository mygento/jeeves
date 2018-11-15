<?php

namespace Mygento\Sample\Model;

use Magento\Framework\Model\AbstractModel;

class Customeraddress extends AbstractModel implements \Mygento\Sample\Api\Data\CustomeraddressInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mygento\Sample\Model\ResourceModel\Customeraddress::class);
    }
}
