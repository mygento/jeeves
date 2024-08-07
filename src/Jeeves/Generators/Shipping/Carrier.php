<?php

namespace Mygento\Jeeves\Generators\Shipping;

use Mygento\Jeeves\Generators\Common;
use Nette\PhpGenerator\PhpNamespace;

class Carrier extends Common
{
    public function genCarrier(
        string $method,
        string $service,
        string $helper,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('Magento\Quote\Model\Quote\Address\RateRequest');
        $namespace->addUse($helper);
        $namespace->addUse('\Mygento\Shipment\Model\Carrier', 'BaseCarrier');
        $namespace->addUse('\Magento\Framework\Profiler');
        $namespace->addUse('\Mygento\Shipment\Model\AbstractCarrier');
        $namespace->addUse('\Magento\Framework\App\Config\ScopeConfigInterface');
        $namespace->addUse('\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory');
        $namespace->addUse('\Magento\Framework\App\Config\ScopeConfigInterface');
        $namespace->addUse('\Psr\Log\LoggerInterface');
        $class = $namespace->addClass('Carrier');
        $class->setExtends('\Mygento\Shipment\Model\AbstractCarrier');

        $class->addProperty('_code', $method)
            ->setVisibility('protected')->addComment('@var string');

        $class->addProperty('service')->setVisibility('private')->setType($service);

        $construct = $class->addMethod('__construct')
            ->setVisibility('public');

        $construct->addParameter('service')->setType($service);
        $construct->addParameter('baseCarrier')
            ->setType('\Mygento\Shipment\Model\Carrier');
        $construct->addParameter('helper')->setType($helper);
        $construct->addParameter('scopeConfig')
            ->setType('\Magento\Framework\App\Config\ScopeConfigInterface');
        $construct->addParameter('rateErrorFactory')
            ->setType('\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory');
        $construct->addParameter('logger')->setType('\Psr\Log\LoggerInterface');
        $construct->addParameter('data')->setType('array')->setDefaultValue([]);

        $construct->setBody(
            '$this->service = $service;' . PHP_EOL . PHP_EOL
            . 'parent::__construct(' . PHP_EOL
            . self::TAB . '$baseCarrier,' . PHP_EOL
            . self::TAB . '$helper,' . PHP_EOL
            . self::TAB . '$scopeConfig,' . PHP_EOL
            . self::TAB . '$rateErrorFactory,' . PHP_EOL
            . self::TAB . '$logger,' . PHP_EOL
            . self::TAB . '$data' . PHP_EOL
            . ');'
        );

        $collect = $class->addMethod('collectRates')
            ->addComment('@param \Magento\Quote\Model\Quote\Address\RateRequest $request')
            ->addComment('@return \Magento\Framework\DataObject|bool|null')
            ->addComment('@api')
            ->setVisibility('public');

        $collect->addParameter('request')->setType('\Magento\Quote\Model\Quote\Address\RateRequest');
        $collect->setBody(
            'Profiler::start($this->_code . \'_collect_rate\');' . PHP_EOL . PHP_EOL
            . '// Validation' . PHP_EOL
            . '$valid = $this->validateRequest($request);' . PHP_EOL
            . 'if ($valid !== true) {' . PHP_EOL
            . self::TAB . 'return $valid;' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . '$calc = $this->baseCarrier->getCalculateRequest();' . PHP_EOL
            . '$calc->setCity($this->convertCity($request));' . PHP_EOL
            . '$calc->setIndex($request->getDestPostcode());' . PHP_EOL
            . '$calc->setWeight($this->convertWeight($request));' . PHP_EOL
            . '$calc->setOrderSum($request->getBaseSubtotalWithDiscountInclTax());' . PHP_EOL
            . '$calc->setRawRequest($request);' . PHP_EOL . PHP_EOL
            . '$methods = $this->service->calculateDeliveryCost($calc);' . PHP_EOL . PHP_EOL
            . '$result = $this->baseCarrier->getResult();' . PHP_EOL
            . 'foreach ($methods as $method) {' . PHP_EOL
            . self::TAB . '$result->append($this->createRateMethod($method));' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . 'Profiler::stop($this->_code . \'_collect_rate\');' . PHP_EOL
            . 'return $result;' . PHP_EOL
        );

        return $namespace;
    }

    public function genService(string $client, string $helper, string $rootNamespace): PhpNamespace
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('\Mygento\Shipment\Model\AbstractService');
        $namespace->addUse('\Magento\Framework\Api\SearchCriteriaBuilder');
        $namespace->addUse('\Magento\Sales\Model\Order');
        $namespace->addUse('\Mygento\Shipment\Api\Data\CalculateRequestInterface');
        $namespace->addUse($helper);
        $namespace->addUse('\Mygento\Shipment\Model\Service', 'BaseService');
        $class = $namespace->addClass('Service');
        $class->setExtends('\Mygento\Shipment\Model\AbstractService');

        $construct = $class->addMethod('__construct')
            ->setVisibility('public');

        $construct->addParameter('client')->setType($client);
        $construct->addParameter('helper')->setType($helper);
        $construct->addParameter('baseService')->setType('\Mygento\Shipment\Model\Service');
        $construct->addParameter('searchBuilder')->setType('\Magento\Framework\Api\SearchCriteriaBuilder');

        $construct->setBody(
            '$this->client = $client;' . PHP_EOL . PHP_EOL
            . 'parent::__construct($baseService, $helper, $searchBuilder);' . PHP_EOL
        );

        $class->addProperty('client')->setType($client)
            ->setVisibility('private');

        $calculate = $class->addMethod('calculateDeliveryCost')
            ->setReturnType('array')
            ->setVisibility('public');

        $calculate->addParameter('request')->setType('\Mygento\Shipment\Api\Data\CalculateRequestInterface');
        $calculate->setBody('return [];');

        $create = $class->addMethod('createOrder')
            ->setVisibility('public');

        $create->addParameter('order')->setType('\Magento\Sales\Model\Order');
        $create->addParameter('data', []);
        $create->setBody('');

        $cancel = $class->addMethod('cancelOrder')
            ->addComment('@param int|string $orderId')
            ->setVisibility('public');

        $cancel->addParameter('orderId');
        $cancel->setBody('');

        $update = $class->addMethod('updateOrderStatus')
            ->setVisibility('public');

        $update->addParameter('order')->setType('\Magento\Sales\Model\Order');
        $update->setBody('');

        return $namespace;
    }

    public function genClient(string $helper, string $rootNamespace): PhpNamespace
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('\Mygento\Shipment\Model\AbstractClient');
        $namespace->addUse($helper);
        $namespace->addUse('\Mygento\Shipment\Model\Client', 'BaseClient');
        $class = $namespace->addClass('Client');
        $class->setExtends('\Mygento\Shipment\Model\AbstractClient');

        $construct = $class->addMethod('__construct')
            ->setVisibility('public');

        $construct->addParameter('helper')->setType($helper);
        $construct->addParameter('baseClient')->setType('\Mygento\Shipment\Model\Client');

        $construct->setBody(
            'parent::__construct($helper, $baseClient);' . PHP_EOL
        );

        $send = $class->addMethod('sendApiRequest')
            ->setVisibility('public');
        $send->addParameter('method')->setType('string');
        $send->addParameter('data');
        $send->addParameter('scopeCode')->setDefaultValue(null);

        return $namespace;
    }
}
