<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ObsoleteSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Obsolete
     * @return \Mygento\SampleModule\Api\Data\ObsoleteInterface[]
     */
    public function getItems();

    /**
     * Set list of Obsolete
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterface[] $items
     */
    public function setItems(array $items);
}
