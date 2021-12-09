<?php

namespace Mygento\SampleModule\Model\SearchCriteria;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;

class BannerStoreFilter implements CustomFilterInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Filter $filter, AbstractDb $collection)
    {
        $collection->addFilter(
            'store_id',
            ['in' => $filter->getValue()]
        );

        return true;
    }
}
