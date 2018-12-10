<?php

namespace Mygento\SampleModule\Api\Data;

interface CustomerAddressSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list of CustomerAddress
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressInterface[]
     */
    public function getItems();

    /**
     * Set list of CustomerAddress
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterface[] $items
     */
    public function setItems(array $items);
}
