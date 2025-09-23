<?php

namespace Mygento\SampleModule\Model;

use Mygento\SampleModule\Helper\Data;
use Mygento\Shipment\Model\AbstractClient;
use Mygento\Shipment\Model\Client as BaseClient;

class Client extends AbstractClient
{
    public function __construct(Data $helper, BaseClient $baseClient)
    {
        parent::__construct($helper, $baseClient);
    }

    public function sendApiRequest(string $method, $data, $scopeCode = null) {}
}
