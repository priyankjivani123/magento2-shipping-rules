<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Rule\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;

class CustomerGroups implements OptionSourceInterface
{
    private GroupCollectionFactory $groupCollectionFactory;

    public function __construct(GroupCollectionFactory $groupCollectionFactory)
    {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    public function toOptionArray(): array
    {
        $groups = [];
        $groups[] = [
            'value' => 0,
            'label' => __('NOT LOGGED IN')
        ];

        $collection = $this->groupCollectionFactory->create();
        foreach ($collection as $group) {
            $groups[] = [
                'value' => (int)$group->getId(),
                'label' => $group->getCustomerGroupCode()
            ];
        }

        return $groups;
    }
}
