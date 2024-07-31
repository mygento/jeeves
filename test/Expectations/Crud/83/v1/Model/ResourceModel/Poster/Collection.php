<?php

namespace Mygento\SampleModule\Model\ResourceModel\Poster;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mygento\SampleModule\Model\Poster;
use Mygento\SampleModule\Model\ResourceModel\Poster as PosterResource;

class Collection extends AbstractCollection
{
    /** @var string */
    protected $_idFieldName = PosterResource::TABLE_PRIMARY_KEY;

    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(
            Poster::class,
            PosterResource::class
        );
    }
}
