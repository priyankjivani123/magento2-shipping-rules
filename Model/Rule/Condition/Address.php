<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Directory\Model\Config\Source\Country as SourceCountry;
use Magento\Directory\Model\Config\Source\Allregion as SourceRegion;

class Address extends AbstractCondition
{
    private SourceCountry $sourceCountry;
    private SourceRegion $sourceRegion;

    public function __construct(
        Context $context,
        SourceCountry $sourceCountry,
        SourceRegion $sourceRegion,
        array $data = []
    ) {
        $this->sourceCountry = $sourceCountry;
        $this->sourceRegion = $sourceRegion;
        parent::__construct($context, $data);
        $this->setType(self::class);
    }

    public function loadAttributeOptions(): self
    {
        $attributes = [
            'base_subtotal' => __('Subtotal (Excl. Tax)'),
            'base_subtotal_with_discount' => __('Subtotal After Discount'),
            'total_qty' => __('Total Items Quantity'),
            'weight' => __('Total Weight'),
            'country_id' => __('Shipping Country'),
            'region' => __('Shipping State/Province Name'),
            'region_id' => __('Shipping State/Province ID'),
            'city' => __('Shipping City'),
            'postcode' => __('Shipping Postcode/ZIP'),
            'street' => __('Shipping Street Address'),
            'coupon_code' => __('Coupon Code')
        ];

        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getAttributeElement(): string
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function getInputType(): string
    {
        return match ($this->getAttribute()) {
            'base_subtotal', 'base_subtotal_with_discount', 'total_qty', 'weight' => 'numeric',
            'country_id', 'region_id' => 'select',
            default => 'string'
        };
    }

    public function getValueElementType(): string
    {
        return match ($this->getAttribute()) {
            'country_id', 'region_id' => 'select',
            default => 'text'
        };
    }

    public function getValueSelectOptions(): array
    {
        if (!$this->hasData('value_select_options')) {
            $options = match ($this->getAttribute()) {
                'country_id' => $this->sourceCountry->toOptionArray(),
                'region_id' => $this->sourceRegion->toOptionArray(),
                default => []
            };
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    public function getDefaultOperatorOptions(): array
    {
        $options = parent::getDefaultOperatorOptions();
        $options['^'] = __('starts with');
        $options['$'] = __('ends with');
        $options['regex'] = __('matches regex');
        return $options;
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model): bool
    {
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if (method_exists($model, 'getShippingAddress')) {
                $address = $model->getShippingAddress();
            } elseif (method_exists($model, 'getAddress')) {
                $address = $model->getAddress();
            }
        }

        if (!$address) {
            return false;
        }

        $attr = $this->getAttribute();
        $value = $address->getData($attr);

        // Special attribute resolution
        if ($attr === 'weight' && $value === null) {
            $value = $address->getWeight() ?: 0.0;
        } elseif ($attr === 'total_qty' && $value === null) {
            $value = 0;
            foreach ($address->getAllItems() as $item) {
                $value += (float)$item->getQty();
            }
        } elseif ($attr === 'coupon_code' && $value === null) {
            $value = $address->getQuote() ? $address->getQuote()->getCouponCode() : '';
        }

        return $this->validateCustomAttribute($value);
    }

    protected function validateCustomAttribute($modelValue): bool
    {
        $op = $this->getOperator();
        $expected = $this->getValue();

        if ($op === '^') {
            return str_starts_with(strtolower((string)$modelValue), strtolower((string)$expected));
        }

        if ($op === '$') {
            return str_ends_with(strtolower((string)$modelValue), strtolower((string)$expected));
        }

        if ($op === 'regex') {
            return $this->validateSafeRegex((string)$expected, (string)$modelValue);
        }

        return parent::validateAttribute($modelValue);
    }

    private function validateSafeRegex(string $pattern, string $subject): bool
    {
        // Wrap with delimiters if missing
        if (!str_starts_with($pattern, '/') || !str_ends_with($pattern, '/')) {
            $pattern = '/' . addcslashes($pattern, '/') . '/i';
        }

        try {
            // Suppress warnings on malformed pattern
            $res = @preg_match($pattern, $subject);
            return $res === 1;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
