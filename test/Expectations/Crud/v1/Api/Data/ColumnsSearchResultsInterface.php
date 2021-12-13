<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ColumnsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Columns
     * @return \Mygento\SampleModule\Api\Data\ColumnsInterface[]
     */
    public function getItems();

    /**
     * Set list of Columns
     * @param \Mygento\SampleModule\Api\Data\ColumnsInterface[] $items
     */
    public function setItems(array $items);
}
