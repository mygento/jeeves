<?php

namespace Mygento\SampleModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Ticket extends AbstractDb
{
    public const TABLE_NAME = 'mygento_sample_module_ticket';
    public const TABLE_PRIMARY_KEY = 'ticket_id';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }
}
