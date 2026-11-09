<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model;

use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea as AreaResource;
use PriyankJivani\ShippingRules\Model\ResourceModel\ShippingArea\CollectionFactory as AreaCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

use PriyankJivani\ShippingRules\Api\Data\ShippingAreaSearchResultInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaSearchResultInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

class ShippingAreaRepository implements ShippingAreaRepositoryInterface
{
    private ShippingAreaFactory $areaFactory;
    private AreaResource $areaResource;
    private AreaCollectionFactory $collectionFactory;
    private ShippingAreaSearchResultInterfaceFactory $searchResultsFactory;
    private ?CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ShippingAreaFactory $areaFactory,
        AreaResource $areaResource,
        AreaCollectionFactory $collectionFactory,
        ShippingAreaSearchResultInterfaceFactory $searchResultsFactory,
        ?CollectionProcessorInterface $collectionProcessor = null
    ) {
        $this->areaFactory = $areaFactory;
        $this->areaResource = $areaResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor ?: \Magento\Framework\App\ObjectManager::getInstance()->get(CollectionProcessorInterface::class);
    }

    public function save(ShippingAreaInterface $area): ShippingAreaInterface
    {
        try {
            $this->areaResource->save($area);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save shipping area: %1', $e->getMessage()), $e);
        }
        return $area;
    }

    public function getById(int $areaId): ShippingAreaInterface
    {
        $area = $this->areaFactory->create();
        $this->areaResource->load($area, $areaId);
        if (!$area->getId()) {
            throw new NoSuchEntityException(__('Shipping area with ID "%1" does not exist.', $areaId));
        }
        return $area;
    }

    public function delete(ShippingAreaInterface $area): bool
    {
        try {
            $this->areaResource->delete($area);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete shipping area: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $areaId): bool
    {
        return $this->delete($this->getById($areaId));
    }

    public function getList(SearchCriteriaInterface $searchCriteria): ShippingAreaSearchResultInterface
    {
        $collection = $this->collectionFactory->create();
        if ($this->collectionProcessor) {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}
