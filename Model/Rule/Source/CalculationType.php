<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;

class CalculationType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => RuleInterface::CALC_REPLACE, 'label' => __('Replace - Replace original rate with calculated amount')],
            ['value' => RuleInterface::CALC_SURCHARGE, 'label' => __('Surcharge - Add additional charge to original rate')],
            ['value' => RuleInterface::CALC_DISCOUNT, 'label' => __('Discount - Deduct calculated amount from original rate')],
            ['value' => RuleInterface::CALC_PARTIAL_REPLACE, 'label' => __('Partial Replace - Replace shipping only for matching products')]
        ];
    }
}
