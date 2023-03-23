<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchResults;
use Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterface;

class ObsoleteSearchResults extends SearchResults implements ObsoleteSearchResultsInterface
{
}
