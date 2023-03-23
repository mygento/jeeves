<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface PosterSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Poster
     * @return \Mygento\SampleModule\Api\Data\PosterInterface[]
     */
    public function getItems();

    /**
     * Set list of Poster
     * @param \Mygento\SampleModule\Api\Data\PosterInterface[] $items
     */
    public function setItems(array $items);
}
