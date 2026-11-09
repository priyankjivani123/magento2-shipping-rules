<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;

class CouponType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => RuleInterface::COUPON_NONE, 'label' => __('Apply to all orders (No coupon required)')],
            ['value' => RuleInterface::COUPON_SPECIFIC, 'label' => __('Apply ONLY WITH specific coupon code')],
            ['value' => RuleInterface::COUPON_EXCLUDE, 'label' => __('Do NOT apply with specific coupon code')]
        ];
    }
}
