<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model;

use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule as RuleResource;
use PriyankJivani\ShippingRules\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class RuleRepository implements RuleRepositoryInterface
{
    private RuleFactory $ruleFactory;
    private RuleResource $ruleResource;
    private RuleCollectionFactory $collectionFactory;
    private SearchResultsInterfaceFactory $searchResultsFactory;

    public function __construct(
        RuleFactory $ruleFactory,
        RuleResource $ruleResource,
        RuleCollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleResource = $ruleResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    public function save(RuleInterface $rule): RuleInterface
    {
        try {
            $this->ruleResource->save($rule);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save shipping rule: %1', $e->getMessage()), $e);
        }
        return $rule;
    }

    public function getById(int $ruleId): RuleInterface
    {
        $rule = $this->ruleFactory->create();
        $this->ruleResource->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Shipping rule with ID "%1" does not exist.', $ruleId));
        }
        return $rule;
    }

    public function delete(RuleInterface $rule): bool
    {
        try {
            $this->ruleResource->delete($rule);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete shipping rule: %1', $e->getMessage()), $e);
        }
        return true;
    }

    public function deleteById(int $ruleId): bool
    {
        return $this->delete($this->getById($ruleId));
    }

    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}
