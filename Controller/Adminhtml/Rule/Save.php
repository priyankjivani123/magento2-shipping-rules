<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PriyankJivani\ShippingRules\Model\RuleFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
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
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $ruleId = (int)($data['rule_id'] ?? 0);
        $rule = $this->ruleFactory->create();

        if ($ruleId) {
            try {
                $rule = $this->ruleRepository->getById($ruleId);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        // Normalize array fields to strings or arrays
        if (isset($data['carriers']) && is_array($data['carriers'])) {
            $data['carriers'] = implode(',', $data['carriers']);
        }
        if (isset($data['methods']) && is_array($data['methods'])) {
            $data['methods'] = implode(',', $data['methods']);
        }
        if (isset($data['days_of_week']) && is_array($data['days_of_week'])) {
            $data['days_of_week'] = implode(',', $data['days_of_week']);
        }

        // Handle condition trees
        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'] ?? [];
            $data['actions'] = $data['rule']['actions'] ?? [];
            unset($data['rule']);
        }

        $rule->loadPost($data);

        try {
            $this->ruleRepository->save($rule);
            $this->messageManager->addSuccessMessage(__('Shipping rule "%1" was saved successfully.', $rule->getName()));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $rule->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while saving the rule: %1', $e->getMessage()));
        }

        return $resultRedirect->setPath('*/*/edit', ['rule_id' => $ruleId]);
    }
}
