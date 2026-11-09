<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea\CollectionFactory as AreaCollectionFactory;
use PriyankJivani\ShippingRules\Model\Service\ShippingAreaMatcher;

class Area extends AbstractCondition
{
    private AreaCollectionFactory $areaCollectionFactory;
    private ShippingAreaMatcher $areaMatcher;

    public function __construct(
        Context $context,
        AreaCollectionFactory $areaCollectionFactory,
        ShippingAreaMatcher $areaMatcher,
        array $data = []
    ) {
        $this->areaCollectionFactory = $areaCollectionFactory;
        $this->areaMatcher = $areaMatcher;
        parent::__construct($context, $data);
        $this->setType(self::class);
    }

    public function loadAttributeOptions(): self
    {
        $this->setAttributeOption([
            'shipping_area_id' => __('Shipping Area')
        ]);
        return $this;
    }

    public function asHtml(): string
    {
        return $this->getTypeElementHtml()
            . __('Shipping Area %1 %2', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    public function getInputType(): string
    {
        return 'select';
    }

    public function getValueElementType(): string
    {
        return 'select';
    }

    public function getValueSelectOptions(): array
    {
        if (!$this->hasData('value_select_options')) {
            $options = [];
            $collection = $this->areaCollectionFactory->create()->addFieldToFilter('is_active', 1);
            foreach ($collection as $area) {
                $options[] = [
                    'value' => (int)$area->getId(),
                    'label' => $area->getName()
                ];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
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

        $targetAreaId = (int)$this->getValue();
        if (!$targetAreaId) {
            return true;
        }

        $isMatch = $this->areaMatcher->matchAddress($targetAreaId, $address);

        return $this->getOperator() === '==' ? $isMatch : !$isMatch;
    }
}
