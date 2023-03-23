<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CustomerAddressSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Customer Address
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressInterface[]
     */
    public function getItems();

    /**
     * Set list of Customer Address
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterface[] $items
     */
    public function setItems(array $items);
}
