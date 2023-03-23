<?php

namespace Mygento\Banan\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Carrier extends \Mygento\Shipment\Model\AbstractCarrier
{
    /** @var string */
    protected $code = 'banan';

    /**
     * @param \Mygento\Banan\Model\Service $service
     * @param \Mygento\Banan\Model\Carrier$carrier
     * @param \Mygento\Banan\Helper\Data$helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Service $service,
        Carrier $carrier,
        \Mygento\Banan\Helper\Data $helper,
        Service $scopeConfig,
        Service $rateErrorFactory,
        Service $logger
    ) {
        $this->service = $service;
        parent::__construct(
            $helper,
            $carrier,
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return bool|\Magento\Framework\DataObject|null
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        \Magento\Framework\Profiler::start($this->code . '_collect_rate');

        //Validation
        $valid = $this->validateRequest($request);
        if ($valid !== true) {
            return $valid;
        }

        $data = [
            'city' => $this->convertCity($request),
            'weight' => $this->convertWeight($request),
            'order_sum' => round($this->getCartTotal(), 0),
            'postcode' => $this->getPostCode($request),
        ];

        $response = $this->service->calculateDeliveryCost($data);
        $result = $this->carrier->getResult();
        foreach ($response as $delivery) {
            $method = [
                'code' => $this->code,
                'title' => $this->helper->getConfig('title'),
                'method' => $this->code,
                'name' => $this->code,
                'price' => $request->getFreeShipping() ? 0 : $delivery['cost'],
                'cost' => $request->getFreeShipping() ? 0 : $delivery['cost'],
                'estimate_dates' => [],
            ];

            $rate = $this->createRateMethod($method);
            $result->append($rate);
        }

        \Magento\Framework\Profiler::stop($this->code . '_collect_rate');

        return $result;
    }
}
