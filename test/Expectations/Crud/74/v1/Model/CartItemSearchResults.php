<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchResults;
use Mygento\SampleModule\Api\Data\CartItemSearchResultsInterface;

class CartItemSearchResults extends SearchResults implements CartItemSearchResultsInterface
{
}
