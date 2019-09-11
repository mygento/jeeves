<?php

namespace Mygento\Banan\Model;

class Service extends \Mygento\Shipment\Model\AbstractService
{
    /** \Mygento\Banan\Model\Client */
    private $client;

    /**
     * @param \Mygento\Banan\Model\Client $client
     * @param \Mygento\Shipment\Model\Service $service
     */
    public function __construct(Client $client, \Mygento\Shipment\Model\Service $service)
    {
        $this->client = $client;
        parent::__construct($service);
    }

    /**
     * @param array $params
     * @return array
     */
    public function calculateDeliveryCost(array $params): array
    {
        return [];
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param array $data
     */
    public function orderCreate(\Magento\Sales\Model\Order $order, array $data = [])
    {
    }

    /**
     * @param int|string $orderId
     */
    public function orderCancel($orderId)
    {
    }
}
