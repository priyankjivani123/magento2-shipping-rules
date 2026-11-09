<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\ResourceModel;

use Magento\Rule\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\AbstractModel;

class Rule extends AbstractResource
{
    protected function _construct()
    {
        $this->_init('priyankjivani_shipping_rule', 'rule_id');
    }

    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->saveStoreRelations($object);
        $this->saveCustomerGroupRelations($object);
        return $this;
    }

    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->loadStoreRelations($object);
        $this->loadCustomerGroupRelations($object);
        return $this;
    }

    private function saveStoreRelations(AbstractModel $object): void
    {
        $connection = $this->getConnection();
        $table = $this->getTable('priyankjivani_shipping_rule_store');
        $ruleId = (int)$object->getId();

        $connection->delete($table, ['rule_id = ?' => $ruleId]);

        $storeIds = $object->getData('store_ids');
        if (is_array($storeIds) && !empty($storeIds)) {
            $data = [];
            foreach ($storeIds as $storeId) {
                $data[] = [
                    'rule_id' => $ruleId,
                    'store_id' => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }

    private function saveCustomerGroupRelations(AbstractModel $object): void
    {
        $connection = $this->getConnection();
        $table = $this->getTable('priyankjivani_shipping_rule_customer_group');
        $ruleId = (int)$object->getId();

        $connection->delete($table, ['rule_id = ?' => $ruleId]);

        $groupIds = $object->getData('customer_group_ids');
        if (is_array($groupIds) && !empty($groupIds)) {
            $data = [];
            foreach ($groupIds as $groupId) {
                $data[] = [
                    'rule_id' => $ruleId,
                    'customer_group_id' => (int)$groupId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }

    private function loadStoreRelations(AbstractModel $object): void
    {
        $connection = $this->getConnection();
        $table = $this->getTable('priyankjivani_shipping_rule_store');
        $select = $connection->select()->from($table, 'store_id')->where('rule_id = ?', (int)$object->getId());
        $storeIds = $connection->fetchCol($select);
        $object->setData('store_ids', array_map('intval', $storeIds));
    }

    private function loadCustomerGroupRelations(AbstractModel $object): void
    {
        $connection = $this->getConnection();
        $table = $this->getTable('priyankjivani_shipping_rule_customer_group');
        $select = $connection->select()->from($table, 'customer_group_id')->where('rule_id = ?', (int)$object->getId());
        $groupIds = $connection->fetchCol($select);
        $object->setData('customer_group_ids', array_map('intval', $groupIds));
    }
}
