<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use Magento\Customer\Model\Config\Source\Group as SourceCustomerGroup;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class Customer extends AbstractCondition
{
    private SourceCustomerGroup $sourceCustomerGroup;
    private OrderCollectionFactory $orderCollectionFactory;

    public function __construct(
        Context $context,
        SourceCustomerGroup $sourceCustomerGroup,
        OrderCollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        $this->sourceCustomerGroup = $sourceCustomerGroup;
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $data);
        $this->setType(self::class);
    }

    public function loadAttributeOptions(): self
    {
        $attributes = [
            'customer_group_id' => __('Customer Group'),
            'customer_id' => __('Customer Entity ID'),
            'email' => __('Customer Email'),
            'is_guest' => __('Guest Customer (1=Yes, 0=No)'),
            'orders_count' => __('Completed Orders Count'),
            'total_spent' => __('Total Lifetime Spent')
        ];

        $this->setAttributeOption($attributes);
        return $this;
    }

    public function getInputType(): string
    {
        return match ($this->getAttribute()) {
            'customer_group_id' => 'select',
            'customer_id', 'is_guest', 'orders_count', 'total_spent' => 'numeric',
            default => 'string'
        };
    }

    public function getValueElementType(): string
    {
        return match ($this->getAttribute()) {
            'customer_group_id' => 'select',
            default => 'text'
        };
    }

    public function getValueSelectOptions(): array
    {
        if (!$this->hasData('value_select_options')) {
            $options = match ($this->getAttribute()) {
                'customer_group_id' => $this->sourceCustomerGroup->toOptionArray(),
                default => []
            };
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    public function validate(\Magento\Framework\Model\AbstractModel $model): bool
    {
        $quote = null;
        if ($model instanceof \Magento\Quote\Model\Quote\Address) {
            $quote = $model->getQuote();
        } elseif ($model instanceof \Magento\Quote\Model\Quote) {
            $quote = $model;
        }

        if (!$quote) {
            return false;
        }

        $attr = $this->getAttribute();
        $modelValue = null;

        if ($attr === 'customer_group_id') {
            $modelValue = (int)$quote->getCustomerGroupId();
        } elseif ($attr === 'customer_id') {
            $modelValue = (int)$quote->getCustomerId();
        } elseif ($attr === 'email') {
            $modelValue = (string)($quote->getCustomerEmail() ?: $quote->getShippingAddress()->getEmail());
        } elseif ($attr === 'is_guest') {
            $modelValue = $quote->getCustomerId() ? 0 : 1;
        } elseif ($attr === 'orders_count' || $attr === 'total_spent') {
            $custId = (int)$quote->getCustomerId();
            if (!$custId) {
                $modelValue = 0;
            } else {
                $orders = $this->orderCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $custId)
                    ->addFieldToFilter('status', ['in' => ['complete', 'processing']]);
                
                if ($attr === 'orders_count') {
                    $modelValue = $orders->getSize();
                } else {
                    $spent = 0.0;
                    foreach ($orders as $ord) {
                        $spent += (float)$ord->getBaseGrandTotal();
                    }
                    $modelValue = $spent;
                }
            }
        }

        return parent::validateAttribute($modelValue);
    }
}
