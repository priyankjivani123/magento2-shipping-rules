<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Area;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea\CollectionFactory;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;

class MassStatus extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::shipping_areas';

    private Filter $filter;
    private CollectionFactory $collectionFactory;
    private ShippingAreaRepositoryInterface $areaRepository;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ShippingAreaRepositoryInterface $areaRepository
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->areaRepository = $areaRepository;
    }

    public function execute()
    {
        $status = (int)$this->getRequest()->getParam('status', 1);
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;

        foreach ($collection as $area) {
            $area->setIsActive((bool)$status);
            $this->areaRepository->save($area);
            $count++;
        }

        $this->messageManager->addSuccessMessage(
            __('A total of %1 shipping area(s) status updated.', $count)
        );

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
