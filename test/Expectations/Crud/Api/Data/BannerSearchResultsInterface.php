<?php

namespace Mygento\SampleModule\Api\Data;

interface BannerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
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
