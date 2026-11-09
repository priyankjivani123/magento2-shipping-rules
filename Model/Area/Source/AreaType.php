<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Area\Source;

use Magento\Framework\Data\OptionSourceInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;

class AreaType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => ShippingAreaInterface::TYPE_ALL, 'label' => __('All')],
            ['value' => ShippingAreaInterface::TYPE_INCLUDE, 'label' => __('Include Selected Only')],
            ['value' => ShippingAreaInterface::TYPE_EXCLUDE, 'label' => __('Exclude Selected (All except)')]
        ];
    }
}
