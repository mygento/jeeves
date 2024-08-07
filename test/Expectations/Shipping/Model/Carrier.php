<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Profiler;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Mygento\SampleModule\Helper\Data;
use Mygento\Shipment\Model\AbstractCarrier;
use Mygento\Shipment\Model\Carrier as BaseCarrier;
use Psr\Log\LoggerInterface;

class Carrier extends AbstractCarrier
{
    /** @var string */
    protected $_code = 'slowcourier';

    private Service $service;

    public function __construct(
        Service $service,
        BaseCarrier $baseCarrier,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        array $data = [],
    ) {
        $this->service = $service;

        parent::__construct(
            $baseCarrier,
            $helper,
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
        Profiler::start($this->_code . '_collect_rate');

        // Validation
        $valid = $this->validateRequest($request);
        if ($valid !== true) {
            return $valid;
        }

        $calc = $this->baseCarrier->getCalculateRequest();
        $calc->setCity($this->convertCity($request));
        $calc->setIndex($request->getDestPostcode());
        $calc->setWeight($this->convertWeight($request));
        $calc->setOrderSum($request->getBaseSubtotalWithDiscountInclTax());
        $calc->setRawRequest($request);

        $methods = $this->service->calculateDeliveryCost($calc);

        $result = $this->baseCarrier->getResult();
        foreach ($methods as $method) {
            $result->append($this->createRateMethod($method));
        }

        Profiler::stop($this->_code . '_collect_rate');

        return $result;
    }
}
