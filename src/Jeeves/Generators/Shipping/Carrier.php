<?php

namespace Mygento\Jeeves\Generators\Shipping;

use Mygento\Jeeves\Generators\Common;
use Nette\PhpGenerator\PhpNamespace;

class Carrier extends Common
{
    public function genCarrier(
        string $method,
        string $service,
        string $carrier,
        string $helper,
        string $rootNamespace,
        string $phpVersion = PHP_VERSION
    ): PhpNamespace {
        $typehint = version_compare($phpVersion, '7.4.0', '>=');
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $namespace->addUse('Magento\Quote\Model\Quote\Address\RateRequest');
        $class = $namespace->addClass('Carrier');
        $class->setExtends('\Mygento\Shipment\Model\AbstractCarrier');

        $class->addProperty('code', $method)
            ->setVisibility('protected')->addComment('@var string');

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $service . ' $service')
            ->addComment('@param ' . $carrier . ' $carrier')
            ->addComment('@param ' . $helper . ' $helper')
            ->addComment('@param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig')
            ->addComment('@param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory')
            ->addComment('@param \Psr\Log\LoggerInterface $logger')
            ->addComment('@param array $data')
            ->setVisibility('public');

        $construct->addParameter('service')->setType($service);
        $construct->addParameter('carrier')->setType($carrier);
        $construct->addParameter('helper')->setType($helper);
        $construct->addParameter('scopeConfig')->setType($service);
        $construct->addParameter('rateErrorFactory')->setType($service);
        $construct->addParameter('logger')->setType($service);

        $construct->setBody(
            '$this->service = $service;' . PHP_EOL
            . 'parent::__construct(' . PHP_EOL
            . '    $helper,' . PHP_EOL
            . '    $carrier,' . PHP_EOL
            . '    $scopeConfig,' . PHP_EOL
            . '    $rateErrorFactory,' . PHP_EOL
            . '    $logger,' . PHP_EOL
            . '    $data' . PHP_EOL
            . ');'
        );

        $collect = $class->addMethod('collectRates')
            ->addComment('@param \Magento\Quote\Model\Quote\Address\RateRequest $request')
            ->addComment('@return \Magento\Framework\DataObject|bool|null')
            ->addComment('@api')
            ->setVisibility('public');

        $collect->addParameter('request')->setType('\Magento\Quote\Model\Quote\Address\RateRequest');
        $collect->setBody(
            '\Magento\Framework\Profiler::start($this->code . \'_collect_rate\');' . PHP_EOL . PHP_EOL
            . '//Validation' . PHP_EOL
            . '$valid = $this->validateRequest($request);' . PHP_EOL
            . 'if ($valid !== true) {' . PHP_EOL
            . '    return $valid;' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . '$data = [' . PHP_EOL
            . '   \'city\' => $this->convertCity($request),' . PHP_EOL
            . '   \'weight\' => $this->convertWeight($request),' . PHP_EOL
            . '   \'order_sum\' => round($this->getCartTotal(), 0),' . PHP_EOL
            . '   \'postcode\' => $this->getPostCode($request),' . PHP_EOL
            . '];' . PHP_EOL . PHP_EOL
            . '$response = $this->service->calculateDeliveryCost($data);' . PHP_EOL
            . '$result = $this->carrier->getResult();' . PHP_EOL
            . 'foreach ($response as $delivery) {' . PHP_EOL
            . '    $method = [' . PHP_EOL
            . '        \'code\' => $this->code,' . PHP_EOL
            . '        \'title\' => $this->helper->getConfig(\'title\'),' . PHP_EOL
            . '        \'method\' => $this->code,' . PHP_EOL
            . '        \'name\' => $this->code,' . PHP_EOL
            . '        \'price\' => $request->getFreeShipping() ? 0 : $delivery[\'cost\'],' . PHP_EOL
            . '        \'cost\' => $request->getFreeShipping() ? 0 : $delivery[\'cost\'],' . PHP_EOL
            . '        \'estimate_dates\' => [],' . PHP_EOL
            . '    ];' . PHP_EOL . PHP_EOL
            . '$rate = $this->createRateMethod($method);' . PHP_EOL
            . '$result->append($rate);' . PHP_EOL
            . '}' . PHP_EOL . PHP_EOL
            . '\Magento\Framework\Profiler::stop($this->code . \'_collect_rate\');' . PHP_EOL
            . 'return $result;' . PHP_EOL
        );

        return $namespace;
    }

    public function genService($client, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $class = $namespace->addClass('Service');
        $class->setExtends('\Mygento\Shipment\Model\AbstractService');

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $client . ' $client')
            ->addComment('@param \Mygento\Shipment\Model\Service $service')
            ->setVisibility('public');

        $construct->addParameter('client')->setType($client);
        $construct->addParameter('service')->setType('\Mygento\Shipment\Model\Service');

        $construct->setBody(
            '$this->client = $client;' . PHP_EOL
            . 'parent::__construct($service);' . PHP_EOL
        );

        $class->addProperty('client')
            ->setVisibility('private')->addComment($client);

        $calculate = $class->addMethod('calculateDeliveryCost')
            ->addComment('@param array $params')
            ->addComment('@return array')
            ->setReturnType('array')
            ->setVisibility('public');

        $calculate->addParameter('params')->setType('array');
        $calculate->setBody('return [];');

        $create = $class->addMethod('orderCreate')
            ->addComment('@param \Magento\Sales\Model\Order $order')
            ->addComment('@param array $data')
            ->setVisibility('public');

        $create->addParameter('order')->setType('\Magento\Sales\Model\Order');
        $create->addParameter('data', [])->setType('array');
        $create->setBody('');

        $cancel = $class->addMethod('orderCancel')
            ->addComment('@param int|string $orderId')
            ->setVisibility('public');

        $cancel->addParameter('orderId');
        $cancel->setBody('');

        return $namespace;
    }

    public function genClient($helper, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Model');
        $class = $namespace->addClass('Client');
        $class->setExtends('\Mygento\Shipment\Model\AbstractClient');

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $helper . ' $helper')
            ->addComment('@param \Mygento\Shipment\Model\Client $client')
            ->setVisibility('public');

        $construct->addParameter('helper')->setType($helper);
        $construct->addParameter('client')->setType('\Mygento\Shipment\Model\Client');

        $construct->setBody(
            '$this->helper = $helper;' . PHP_EOL
            . 'parent::__construct($client);' . PHP_EOL
        );

        $class->addProperty('helper')
            ->setVisibility('private')->addComment($helper);

        return $namespace;
    }
}
