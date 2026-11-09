<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Controller\Adminhtml\Area;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterfaceFactory;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'PriyankJivani_ShippingRules::shipping_areas';

    private ShippingAreaRepositoryInterface $areaRepository;
    private ShippingAreaInterfaceFactory $areaFactory;

    public function __construct(
        Context $context,
        ShippingAreaRepositoryInterface $areaRepository,
        ShippingAreaInterfaceFactory $areaFactory
    ) {
        parent::__construct($context);
        $this->areaRepository = $areaRepository;
        $this->areaFactory = $areaFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $areaId = !empty($data['area_id']) ? (int)$data['area_id'] : null;

        try {
            if ($areaId) {
                $area = $this->areaRepository->getById($areaId);
            } else {
                $area = $this->areaFactory->create();
            }

            $area->setName($data['name']);
            $area->setDescription($data['description'] ?? '');
            $area->setIsActive(!empty($data['is_active']));

            // Parse countries
            $countries = !empty($data['countries']) ? (array)$data['countries'] : [];
            $area->setCountries($countries);

            // Parse regions
            $regions = !empty($data['regions']) ? (array)$data['regions'] : [];
            $area->setRegions($regions);

            // Parse cities
            $cities = [];
            if (!empty($data['cities'])) {
                $cities = is_array($data['cities']) ? $data['cities'] : preg_split('/[\r\n,]+/', (string)$data['cities'], -1, PREG_SPLIT_NO_EMPTY);
            }
            $area->setCities(array_map('trim', (array)$cities));

            // Parse postcodes
            $postcodes = [];
            if (!empty($data['postcodes'])) {
                $postcodes = is_array($data['postcodes']) ? $data['postcodes'] : preg_split('/[\r\n,]+/', (string)$data['postcodes'], -1, PREG_SPLIT_NO_EMPTY);
            }
            $area->setPostcodes(array_map('trim', (array)$postcodes));

            // Parse address lines
            $addressLines = [];
            if (!empty($data['address_lines'])) {
                $addressLines = is_array($data['address_lines']) ? $data['address_lines'] : preg_split('/[\r\n]+/', (string)$data['address_lines'], -1, PREG_SPLIT_NO_EMPTY);
            }
            $area->setAddressLines(array_map('trim', (array)$addressLines));

            $area->setAddressLinesType($data['address_lines_type'] ?? 'include');

            $this->areaRepository->save($area);

            $this->messageManager->addSuccessMessage(__('You saved the shipping area.'));

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['area_id' => $area->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Throwable $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the shipping area.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['area_id' => $areaId]);
    }
}
