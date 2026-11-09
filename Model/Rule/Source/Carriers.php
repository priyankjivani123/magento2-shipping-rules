<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config as ShippingConfig;

class Carriers implements OptionSourceInterface
{
    private ShippingConfig $shippingConfig;

    public function __construct(ShippingConfig $shippingConfig)
    {
        $this->shippingConfig = $shippingConfig;
    }

    public function toOptionArray(): array
    {
        $options = [];
        $carriers = $this->shippingConfig->getAllCarriers();

        foreach ($carriers as $code => $carrierModel) {
            $title = $carrierModel->getConfigData('title') ?: $code;
            $options[] = [
                'value' => $code,
                'label' => sprintf('%s (%s)', $title, $code)
            ];
        }

        return $options;
    }
}
