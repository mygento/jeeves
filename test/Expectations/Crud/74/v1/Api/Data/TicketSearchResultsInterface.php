<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface TicketSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Ticket
     * @return \Mygento\SampleModule\Api\Data\TicketInterface[]
     */
    public function getItems();

    /**
     * Set list of Ticket
     * @param \Mygento\SampleModule\Api\Data\TicketInterface[] $items
     */
    public function setItems(array $items);
}
