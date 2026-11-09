<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Area\Source;

use Magento\Framework\Data\OptionSourceInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;

class AddressLinesType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => ShippingAreaInterface::ADDR_TYPE_ALL, 'label' => __('All Address Lines')],
            ['value' => ShippingAreaInterface::ADDR_TYPE_CONTAINS, 'label' => __('Address Lines Contain')],
            ['value' => ShippingAreaInterface::ADDR_TYPE_NOT_CONTAINS, 'label' => __('Address Lines Do Not Contain')]
        ];
    }
}
