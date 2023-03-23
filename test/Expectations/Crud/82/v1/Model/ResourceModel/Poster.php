<?php

namespace Mygento\SampleModule\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Poster extends AbstractDb
{
    public const TABLE_NAME = 'mygento_sample_module_poster';
    public const TABLE_PRIMARY_KEY = 'id';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, self::TABLE_PRIMARY_KEY);
    }
}
