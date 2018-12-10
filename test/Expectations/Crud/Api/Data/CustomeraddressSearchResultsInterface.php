<?php

namespace Mygento\Samplemodule\Api\Data;

interface CustomeraddressSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list of customeraddress
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface[]
     */
    public function getItems();

    /**
     * Set list of customeraddress
     * @param \Mygento\Samplemodule\Api\Data\CustomeraddressInterface[] $items
     */
    public function setItems(array $items);
}
