<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CartItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Cart Item
     * @return \Mygento\SampleModule\Api\Data\CartItemInterface[]
     */
    public function getItems();

    /**
     * Set list of Cart Item
     * @param \Mygento\SampleModule\Api\Data\CartItemInterface[] $items
     */
    public function setItems(array $items);
}
