<?php

namespace Mygento\SampleModule\Model\SearchCriteria;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor\CustomFilterInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Mygento\SampleModule\Api\Data\CardInterface;

class CardStoreFilter implements CustomFilterInterface
{
    /**
     * @inheritDoc
     */
    public function apply(Filter $filter, AbstractDb $collection): bool
    {
        $collection->addFilter(
            CardInterface::STORE_ID,
            ['in' => $filter->getValue()]
        );

        return true;
    }
}
