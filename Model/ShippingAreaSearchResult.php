<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model;

use Magento\Framework\Api\SearchResults;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaSearchResultInterface;

class ShippingAreaSearchResult extends SearchResults implements ShippingAreaSearchResultInterface
{
}
