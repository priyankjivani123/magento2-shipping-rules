<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PriyankJivani\ShippingRules\Model\RuleFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;

class Duplicate extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::manage_rules';

    private RuleFactory $ruleFactory;
    private RuleRepositoryInterface $ruleRepository;

    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context);
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $ruleId = (int)$this->getRequest()->getParam('rule_id');

        if (!$ruleId) {
            $this->messageManager->addErrorMessage(__('Please select a shipping rule to duplicate.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $originalRule = $this->ruleRepository->getById($ruleId);
            $duplicate = $this->ruleFactory->create();
            $data = $originalRule->getData();
            unset($data['rule_id'], $data['created_at'], $data['updated_at']);

            $duplicate->setData($data);
            $duplicate->setName(__('Copy of %1', $originalRule->getName()));
            $duplicate->setIsActive(false); // Duplicated rule is disabled by default for safety
            $duplicate->setStoreIds($originalRule->getStoreIds());
            $duplicate->setCustomerGroupIds($originalRule->getCustomerGroupIds());

            $this->ruleRepository->save($duplicate);
            $this->messageManager->addSuccessMessage(__('Shipping rule duplicated successfully. You can now edit it.'));
            return $resultRedirect->setPath('*/*/edit', ['rule_id' => $duplicate->getId()]);
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('Error duplicating shipping rule: %1', $e->getMessage()));
            return $resultRedirect->setPath('*/*/');
        }
    }
}
