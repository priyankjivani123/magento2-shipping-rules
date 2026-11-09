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
        $collection = $this->groupCollectionFactory->create();
        $seen = [];

        foreach ($collection as $group) {
            $id = (int)$group->getId();
            $seen[$id] = true;
            $code = $group->getCustomerGroupCode();
            $groups[] = [
                'value' => $id,
                'label' => $code ? (string)$code : __('NOT LOGGED IN')
            ];
        }

        if (!isset($seen[0])) {
            array_unshift($groups, [
                'value' => 0,
                'label' => __('NOT LOGGED IN')
            ]);
        }

        return $groups;
    }
}
