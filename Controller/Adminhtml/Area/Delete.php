<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Area;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::shipping_areas';

    private ShippingAreaRepositoryInterface $areaRepository;

    public function __construct(
        Context $context,
        ShippingAreaRepositoryInterface $areaRepository
    ) {
        parent::__construct($context);
        $this->areaRepository = $areaRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('area_id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            try {
                $this->areaRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the shipping area.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['area_id' => $id]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a shipping area to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
