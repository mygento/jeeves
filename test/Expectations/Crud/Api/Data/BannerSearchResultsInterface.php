<?php

namespace Mygento\Sample\Api\Data;

interface BannerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list of banner
     * @return \Mygento\Sample\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set list of banner
     * @param \Mygento\Sample\Api\Data\BannerInterface[] $items
     */
    public function setItems(array $items);
}
