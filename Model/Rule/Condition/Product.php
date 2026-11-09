<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Catalog\Model\Product\Type as ProductType;

class Product extends AbstractCondition
{
    private ProductType $productType;

    public function __construct(
        Context $context,
        ProductType $productType,
        array $data = []
    ) {
        $this->productType = $productType;
        parent::__construct($context, $data);
        $this->setType(self::class);
    }

    public function loadAttributeOptions(): self
    {
        $this->setAttributeOption([
            'sku' => __('Product SKU'),
            'type_id' => __('Product Type'),
            'category_ids' => __('Category IDs (comma-separated)'),
            'price' => __('Product Base Price'),
            'weight' => __('Product Unit Weight'),
            'qty' => __('Cart Item Quantity')
        ]);
        return $this;
    }

    public function getInputType(): string
    {
        return match ($this->getAttribute()) {
            'price', 'weight', 'qty' => 'numeric',
            'type_id' => 'select',
            default => 'string'
        };
    }

    public function getValueElementType(): string
    {
        return match ($this->getAttribute()) {
            'type_id' => 'select',
            default => 'text'
        };
    }

    public function getValueSelectOptions(): array
    {
        if (!$this->hasData('value_select_options')) {
            $options = match ($this->getAttribute()) {
                'type_id' => $this->productType->getOptionArray(),
                default => []
            };
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model): bool
    {
        // $model can be \Magento\Quote\Model\Quote\Item or \Magento\Catalog\Model\Product
        $attr = $this->getAttribute();
        $modelValue = null;

        if ($model instanceof \Magento\Quote\Model\Quote\Item) {
            $product = $model->getProduct();
            if ($attr === 'sku') {
                $modelValue = $model->getSku();
            } elseif ($attr === 'price') {
                $modelValue = (float)$model->getBasePrice();
            } elseif ($attr === 'qty') {
                $modelValue = (float)$model->getQty();
            } elseif ($attr === 'weight') {
                $modelValue = (float)$model->getWeight();
            } elseif ($attr === 'category_ids') {
                $modelValue = $product ? (array)$product->getCategoryIds() : [];
            } elseif ($attr === 'type_id') {
                $modelValue = $product ? $product->getTypeId() : $model->getProductType();
            }
        } elseif ($model instanceof \Magento\Catalog\Model\Product) {
            if ($attr === 'category_ids') {
                $modelValue = (array)$model->getCategoryIds();
            } else {
                $modelValue = $model->getData($attr);
            }
        }

        if ($attr === 'category_ids') {
            return $this->validateCategoryArray((array)$modelValue);
        }

        return parent::validateAttribute($modelValue);
    }

    private function validateCategoryArray(array $categories): bool
    {
        $expected = array_map('trim', explode(',', (string)$this->getValue()));
        $intersect = array_intersect($categories, $expected);

        return match ($this->getOperator()) {
            '==', '{}', '()' => !empty($intersect),
            '!=', '!{}', '!()' => empty($intersect),
            default => !empty($intersect)
        };
    }
}
