<?php

namespace Mygento\Sample\Api\Data;

interface CustomeraddressSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list of customeraddress
     * @return \Mygento\Sample\Api\Data\CustomeraddressInterface[]
     */
    public function getItems();

    /**
     * Set list of customeraddress
     * @param \Mygento\Sample\Api\Data\CustomeraddressInterface[] $items
     */
    public function setItems(array $items);
}
