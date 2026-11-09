<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Api;

use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

use PriyankJivani\ShippingRules\Api\Data\RuleSearchResultInterface;

interface RuleRepositoryInterface
{
    /**
     * Save shipping rule.
     *
     * @param RuleInterface $rule
     * @return RuleInterface
     * @throws CouldNotSaveException
     */
    public function save(RuleInterface $rule): RuleInterface;

    /**
     * Get shipping rule by ID.
     *
     * @param int $ruleId
     * @return RuleInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $ruleId): RuleInterface;

    /**
     * Delete shipping rule.
     *
     * @param RuleInterface $rule
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(RuleInterface $rule): bool;

    /**
     * Delete shipping rule by ID.
     *
     * @param int $ruleId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $ruleId): bool;

    /**
     * Get list of shipping rules matching search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return RuleSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): RuleSearchResultInterface;
}
