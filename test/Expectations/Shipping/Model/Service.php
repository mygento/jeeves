<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order;
use Mygento\SampleModule\Helper\Data;
use Mygento\Shipment\Api\Data\CalculateRequestInterface;
use Mygento\Shipment\Model\AbstractService;
use Mygento\Shipment\Model\Service as BaseService;

class Service extends AbstractService
{
    private Client $client;

    public function __construct(
        Client $client,
        Data $helper,
        BaseService $baseService,
        SearchCriteriaBuilder $searchBuilder,
    ) {
        $this->client = $client;

        parent::__construct($baseService, $helper, $searchBuilder);
    }

    public function calculateDeliveryCost(CalculateRequestInterface $request): array
    {
        return [];
    }

    public function createOrder(Order $order, $data = [])
    {
    }

    /**
     * @param int|string $orderId
     */
    public function cancelOrder($orderId)
    {
    }

    public function updateOrderStatus(Order $order)
    {
    }
}
