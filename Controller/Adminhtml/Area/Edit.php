<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Area;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::shipping_areas';

    private PageFactory $resultPageFactory;
    private ShippingAreaRepositoryInterface $areaRepository;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ShippingAreaRepositoryInterface $areaRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->areaRepository = $areaRepository;
    }

    public function execute()
    {
        $areaId = (int)$this->getRequest()->getParam('area_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('PriyankJivani_ShippingRules::shipping_areas');

        if ($areaId) {
            try {
                $area = $this->areaRepository->getById($areaId);
                $resultPage->getConfig()->getTitle()->prepend(__('Edit Shipping Area "%1"', $area->getName()));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('This shipping area no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Shipping Area'));
        }

        return $resultPage;
    }
}
