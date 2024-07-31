<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchResults;
use Mygento\SampleModule\Api\Data\TicketSearchResultsInterface;

class TicketSearchResults extends SearchResults implements TicketSearchResultsInterface
{
}
