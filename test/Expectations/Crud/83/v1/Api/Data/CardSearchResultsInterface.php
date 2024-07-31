<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface CardSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Card
     * @return \Mygento\SampleModule\Api\Data\CardInterface[]
     */
    public function getItems();

    /**
     * Set list of Card
     * @param \Mygento\SampleModule\Api\Data\CardInterface[] $items
     */
    public function setItems(array $items);
}
