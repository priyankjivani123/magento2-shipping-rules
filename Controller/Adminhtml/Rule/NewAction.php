<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class NewAction extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    public function execute()
    {
        $this->_forward('edit');
    }
}
