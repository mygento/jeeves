<?php

namespace Mygento\SampleModule\Model;

class Client extends \Mygento\Shipment\Model\AbstractClient
{
    /** \Mygento\SampleModule\Helper\Data */
    private $helper;

    /**
     * @param \Mygento\SampleModule\Helper\Data $helper
     * @param \Mygento\Shipment\Model\Client $client
     */
    public function __construct(\Mygento\SampleModule\Helper\Data $helper, \Mygento\Shipment\Model\Client $client)
    {
        $this->helper = $helper;
        parent::__construct($client);
    }
}
