<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Area;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;

class Duplicate extends Action
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
                $area = $this->areaRepository->getById($id);
                $duplicate = clone $area;
                $duplicate->setId(null);
                $duplicate->setName($area->getName() . ' (Duplicate)');
                $duplicate->setIsActive(false);

                $this->areaRepository->save($duplicate);
                $this->messageManager->addSuccessMessage(__('You duplicated the shipping area.'));
                return $resultRedirect->setPath('*/*/edit', ['area_id' => $duplicate->getId()]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
