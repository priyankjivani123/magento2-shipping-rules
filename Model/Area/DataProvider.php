<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Area;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea\CollectionFactory;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;

class DataProvider extends AbstractDataProvider
{
    private DataPersistorInterface $dataPersistor;
    private ShippingAreaRepositoryInterface $areaRepository;
    private array $loadedData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        ShippingAreaRepositoryInterface $areaRepository,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->areaRepository = $areaRepository;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $area = $this->areaRepository->getById((int)$model->getId());
            $data = $area->getData();

            $data['countries'] = $area->getCountries();
            $data['regions'] = $area->getRegions();
            $data['cities'] = is_array($area->getCities()) ? implode("\n", $area->getCities()) : (string)$area->getCities();
            $data['postcodes'] = is_array($area->getPostcodes()) ? implode("\n", $area->getPostcodes()) : (string)$area->getPostcodes();
            $data['address_lines'] = is_array($area->getAddressLines()) ? implode("\n", $area->getAddressLines()) : (string)$area->getAddressLines();

            $this->loadedData[$area->getId()] = $data;
        }

        $data = $this->dataPersistor->get('shippingrules_area');
        if (!empty($data)) {
            $areaId = $data['area_id'] ?? null;
            $this->loadedData[$areaId] = $data;
            $this->dataPersistor->clear('shippingrules_area');
        }

        return $this->loadedData;
    }
}
