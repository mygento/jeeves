<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchResults;
use Mygento\SampleModule\Api\Data\CardSearchResultsInterface;

class CardSearchResults extends SearchResults implements CardSearchResultsInterface
{
}
