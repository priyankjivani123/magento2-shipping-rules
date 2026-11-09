<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Combine as MagentoCombine;
use Magento\Rule\Model\Condition\Context;

class ProductCombine extends MagentoCombine
{
    private ProductFactory $productConditionFactory;

    public function __construct(
        Context $context,
        ProductFactory $productConditionFactory,
        array $data = []
    ) {
        $this->productConditionFactory = $productConditionFactory;
        parent::__construct($context, $data);
        $this->setType(self::class);
    }

    public function getNewChildSelectOptions(): array
    {
        $productCondition = $this->productConditionFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $productOptions = [];
        foreach ($productAttributes as $code => $label) {
            $productOptions[] = [
                'value' => Product::class . '|' . $code,
                'label' => $label
            ];
        }

        return array_merge_recursive(
            parent::getNewChildSelectOptions(),
            [
                [
                    'value' => self::class,
                    'label' => __('Product Conditions Combination')
                ],
                [
                    'label' => __('Product Attributes'),
                    'value' => $productOptions
                ]
            ]
        );
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model): bool
    {
        // Validates if quote contains matching items
        $items = [];
        if ($model instanceof \Magento\Quote\Model\Quote\Address) {
            $items = $model->getAllItems();
        } elseif ($model instanceof \Magento\Quote\Model\Quote) {
            $items = $model->getAllItems();
        } elseif ($model instanceof \Magento\Quote\Model\Quote\Item) {
            return parent::validate($model);
        }

        if (empty($items)) {
            return false;
        }

        $all = $this->getAggregator() === 'all';
        $true = (bool)$this->getValue();

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $itemValid = parent::validate($item);
            if (!$all && $itemValid === $true) {
                return true;
            }
            if ($all && $itemValid !== $true) {
                return false;
            }
        }

        return $all;
    }
}
