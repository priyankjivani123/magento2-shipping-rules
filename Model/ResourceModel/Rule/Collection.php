<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\ResourceModel\Rule;

use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;
use PriyankJivani\ShippingRules\Model\Rule as RuleModel;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule as RuleResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'rule_id';

    protected function _construct()
    {
        $this->_init(RuleModel::class, RuleResource::class);
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->count()) {
            $connection = $this->getConnection();
            $ruleIds = $this->getColumnValues('rule_id');

            // Batch load store relations
            $storeTable = $this->getTable('priyankjivani_shipping_rule_store');
            $storeSelect = $connection->select()
                ->from($storeTable, ['rule_id', 'store_id'])
                ->where('rule_id IN (?)', $ruleIds);
            $storeRows = $connection->fetchAll($storeSelect);
            $storesByRule = [];
            foreach ($storeRows as $row) {
                $storesByRule[$row['rule_id']][] = (int)$row['store_id'];
            }

            // Batch load customer group relations
            $groupTable = $this->getTable('priyankjivani_shipping_rule_customer_group');
            $groupSelect = $connection->select()
                ->from($groupTable, ['rule_id', 'customer_group_id'])
                ->where('rule_id IN (?)', $ruleIds);
            $groupRows = $connection->fetchAll($groupSelect);
            $groupsByRule = [];
            foreach ($groupRows as $row) {
                $groupsByRule[$row['rule_id']][] = (int)$row['customer_group_id'];
            }

            foreach ($this->_items as $item) {
                $ruleId = $item->getId();
                $item->setData('store_ids', $storesByRule[$ruleId] ?? []);
                $item->setData('customer_group_ids', $groupsByRule[$ruleId] ?? []);
            }
        }
        return $this;
    }
}
