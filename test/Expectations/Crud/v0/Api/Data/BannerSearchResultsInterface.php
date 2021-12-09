<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface BannerSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list of Banner
     * @return \Mygento\SampleModule\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set list of Banner
     * @param \Mygento\SampleModule\Api\Data\BannerInterface[] $items
     */
    public function setItems(array $items);
}
