<?php

namespace Mygento\Banan\Model;

class Client extends \Mygento\Shipment\Model\AbstractClient
{
    /** \Mygento\Banan\Helper\Data */
    private $helper;

    /**
     * @param \Mygento\Banan\Helper\Data $helper
     * @param \Mygento\Shipment\Model\Client $client
     */
    public function __construct(\Mygento\Banan\Helper\Data $helper, \Mygento\Shipment\Model\Client $client)
    {
        $this->helper = $helper;
        parent::__construct($client);
    }
}
