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
}
