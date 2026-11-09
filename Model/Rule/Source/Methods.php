<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config as ShippingConfig;

class Methods implements OptionSourceInterface
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

        foreach ($carriers as $carrierCode => $carrierModel) {
            $carrierTitle = $carrierModel->getConfigData('title') ?: $carrierCode;
            $methods = $carrierModel->getAllowedMethods();

            if (is_array($methods)) {
                foreach ($methods as $methodCode => $methodTitle) {
                    $options[] = [
                        'value' => $carrierCode . '_' . $methodCode,
                        'label' => sprintf('%s - %s (%s_%s)', $carrierTitle, $methodTitle ?: $methodCode, $carrierCode, $methodCode)
                    ];
                }
            } else {
                $options[] = [
                    'value' => $carrierCode . '_' . $carrierCode,
                    'label' => sprintf('%s (%s)', $carrierTitle, $carrierCode)
                ];
            }
        }

        return $options;
    }
}
