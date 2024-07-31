<?php

namespace Mygento\SampleModule\Model\ResourceModel\Ticket;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Model\ResourceModel\Ticket as TicketResource;
use Mygento\SampleModule\Model\Ticket;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = TicketResource::TABLE_PRIMARY_KEY;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            Ticket::class,
            TicketResource::class
        );
    }
}
