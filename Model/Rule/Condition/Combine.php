<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Combine as MagentoCombine;
use Magento\Rule\Model\Condition\Context;
use Magento\Framework\Event\ManagerInterface;

class Combine extends MagentoCombine
{
    private ManagerInterface $eventManager;
    private AddressFactory $addressFactory;
    private CustomerFactory $customerFactory;
    private AreaFactory $areaFactory;
    private ProductCombineFactory $productCombineFactory;

    public function __construct(
        Context $context,
        ManagerInterface $eventManager,
        AddressFactory $addressFactory,
        CustomerFactory $customerFactory,
        AreaFactory $areaFactory,
        ProductCombineFactory $productCombineFactory,
        array $data = []
    ) {
        $this->eventManager = $eventManager;
        $this->addressFactory = $addressFactory;
        $this->customerFactory = $customerFactory;
        $this->areaFactory = $areaFactory;
        $this->productCombineFactory = $productCombineFactory;
        parent::__construct($context, $data);
        $this->setType(self::class);
    }

    public function getNewChildSelectOptions(): array
    {
        $addressCondition = $this->addressFactory->create();
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();

        $addressOptions = [];
        foreach ($addressAttributes as $code => $label) {
            $addressOptions[] = [
                'value' => Address::class . '|' . $code,
                'label' => $label
            ];
        }

        $customerCondition = $this->customerFactory->create();
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();

        $customerOptions = [];
        foreach ($customerAttributes as $code => $label) {
            $customerOptions[] = [
                'value' => Customer::class . '|' . $code,
                'label' => $label
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => self::class,
                    'label' => __('Conditions Combination')
                ],
                [
                    'value' => Area::class,
                    'label' => __('Shipping Area')
                ],
                [
                    'label' => __('Cart & Address Attributes'),
                    'value' => $addressOptions
                ],
                [
                    'label' => __('Customer Attributes'),
                    'value' => $customerOptions
                ],
                [
                    'value' => ProductCombine::class,
                    'label' => __('Product Subselection Combination')
                ]
            ]
        );

        return $conditions;
    }
}
