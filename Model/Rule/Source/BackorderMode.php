<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;

class BackorderMode implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => RuleInterface::BACKORDER_ALL, 'label' => __('All Orders (In-stock and Backorders)')],
            ['value' => RuleInterface::BACKORDER_ONLY, 'label' => __('Backorders Only (Cart must contain backordered items)')],
            ['value' => RuleInterface::BACKORDER_EXCLUDE, 'label' => __('Non-Backorders Only (Cart must contain only in-stock items)')]
        ];
    }
}
