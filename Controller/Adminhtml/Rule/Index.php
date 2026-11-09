<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    private PageFactory $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('PriyankJivani_ShippingRules::manage_rules');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Shipping Rules'));
        return $resultPage;
    }
}
