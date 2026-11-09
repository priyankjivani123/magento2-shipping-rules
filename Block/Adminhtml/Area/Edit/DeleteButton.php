<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Block\Adminhtml\Area\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $areaId = $this->getAreaId();
        if (!$areaId) {
            return [];
        }

        return [
            'label' => __('Delete Shipping Area'),
            'class' => 'delete',
            'on_click' => sprintf("deleteConfirm('%s', '%s')", __('Are you sure you want to delete this shipping area?'), $this->getUrl('*/*/delete', ['area_id' => $areaId])),
            'sort_order' => 20,
        ];
    }
}
