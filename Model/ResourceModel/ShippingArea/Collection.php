<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PriyankJivani\ShippingRules\Model\ShippingArea as AreaModel;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea as AreaResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'area_id';

    protected function _construct()
    {
        $this->_init(AreaModel::class, AreaResource::class);
    }
}
