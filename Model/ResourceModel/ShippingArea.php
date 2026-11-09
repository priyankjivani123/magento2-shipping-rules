<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShippingArea extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('priyankjivani_shipping_area', 'area_id');
    }
}
