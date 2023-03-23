<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchResults;
use Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterface;

class CustomerAddressSearchResults extends SearchResults implements CustomerAddressSearchResultsInterface
{
}
