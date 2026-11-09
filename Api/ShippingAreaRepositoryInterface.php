<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Api;

use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

interface ShippingAreaRepositoryInterface
{
    /**
     * Save shipping area.
     *
     * @param ShippingAreaInterface $area
     * @return ShippingAreaInterface
     * @throws CouldNotSaveException
     */
    public function save(ShippingAreaInterface $area): ShippingAreaInterface;

    /**
     * Get shipping area by ID.
     *
     * @param int $areaId
     * @return ShippingAreaInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $areaId): ShippingAreaInterface;

    /**
     * Delete shipping area.
     *
     * @param ShippingAreaInterface $area
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShippingAreaInterface $area): bool;

    /**
     * Delete shipping area by ID.
     *
     * @param int $areaId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $areaId): bool;

    /**
     * Get list of shipping areas matching criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
