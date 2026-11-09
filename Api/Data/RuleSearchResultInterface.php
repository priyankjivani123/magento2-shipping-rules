<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface RuleSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get shipping rules list.
     *
     * @return \PriyankJivani\ShippingRules\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set shipping rules list.
     *
     * @param \PriyankJivani\ShippingRules\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
