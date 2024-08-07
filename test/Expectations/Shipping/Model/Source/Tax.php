<?php

namespace Mygento\SampleModule\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Tax implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 0, 'label' => __('VAT0')],
            ['value' => 10, 'label' => __('VAT10')],
            ['value' => 20, 'label' => __('VAT20')],
            ['value' => null, 'label' => __('VAT Free')],
        ];
    }
}
