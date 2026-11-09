<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Block\Adminhtml\Rule\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $ruleId = $this->getRuleId();
        if (!$ruleId) {
            return [];
        }

        return [
            'label' => __('Delete Rule'),
            'class' => 'delete',
            'on_click' => sprintf("deleteConfirm('%s', '%s')", __('Are you sure you want to delete this rule?'), $this->getUrl('*/*/delete', ['rule_id' => $ruleId])),
            'sort_order' => 20,
        ];
    }
}
