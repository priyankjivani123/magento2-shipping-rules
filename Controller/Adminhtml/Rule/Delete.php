<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    private RuleRepositoryInterface $ruleRepository;

    public function __construct(
        Context $context,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ruleId = (int)$this->getRequest()->getParam('rule_id');

        if ($ruleId) {
            try {
                $this->ruleRepository->deleteById($ruleId);
                $this->messageManager->addSuccessMessage(__('The shipping rule has been deleted.'));
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Error deleting shipping rule: %1', $e->getMessage()));
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $ruleId]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We could not find a shipping rule to delete.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
