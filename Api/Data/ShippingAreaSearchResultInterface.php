<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ShippingAreaSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get shipping areas list.
     *
     * @return \PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface[]
     */
    public function getItems();

    /**
     * Set shipping areas list.
     *
     * @param \PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
