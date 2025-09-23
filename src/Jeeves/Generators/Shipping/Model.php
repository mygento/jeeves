<?php

namespace Mygento\Jeeves\Generators\Shipping;

use Mygento\Jeeves\Generators\Common;
use Nette\PhpGenerator\PhpNamespace;

class Model extends Common
{
    public function genTax(
        string $rootNamespace,
        string $phpVersion = PHP_VERSION,
    ): PhpNamespace {
        $namespace = new PhpNamespace($rootNamespace . '\Model\Source');
        $namespace->addUse('\Magento\Framework\Data\OptionSourceInterface');

        $class = $namespace->addClass('Tax');
        $class->setImplements(['\Magento\Framework\Data\OptionSourceInterface']);

        $options = $class->addMethod('toOptionArray')->setVisibility('public')->setReturnType('array');

        $options->setBody(
            'return [' . PHP_EOL
            . self::TAB . "['value' => 0, 'label' => __('VAT0')]," . PHP_EOL
            . self::TAB . "['value' => 10, 'label' => __('VAT10')]," . PHP_EOL
            . self::TAB . "['value' => 20, 'label' => __('VAT20')]," . PHP_EOL
            . self::TAB . "['value' => null, 'label' => __('VAT Free')]," . PHP_EOL
            . '];' . PHP_EOL,
        );

        return $namespace;
    }
}
