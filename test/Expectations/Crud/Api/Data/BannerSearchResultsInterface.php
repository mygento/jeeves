<?php

namespace Mygento\Samplemodule\Api\Data;

interface BannerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get list of banner
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface[]
     */
    public function getItems();

    /**
     * Set list of banner
     * @param \Mygento\Samplemodule\Api\Data\BannerInterface[] $items
     */
    public function setItems(array $items);
}
