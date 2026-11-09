<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use PriyankJivani\ShippingRules\Model\RuleFactory;
use Magento\Framework\Registry;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    private PageFactory $resultPageFactory;
    private RuleFactory $ruleFactory;
    private Registry $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RuleFactory $ruleFactory,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->ruleFactory = $ruleFactory;
        $this->registry = $registry;
    }

    public function execute()
    {
        $ruleId = (int)$this->getRequest()->getParam('rule_id');
        $rule = $this->ruleFactory->create();

        if ($ruleId) {
            $rule->load($ruleId);
            if (!$rule->getId()) {
                $this->messageManager->addErrorMessage(__('This shipping rule no longer exists.'));
                return $this->_redirect('*/*/');
            }
        }

        $this->registry->register('current_shipping_rule', $rule);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('PriyankJivani_ShippingRules::manage_rules');
        $title = $ruleId ? __('Edit Shipping Rule: %1', $rule->getName()) : __('New Shipping Rule');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }
}
