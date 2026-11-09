<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Plugin;

use Magento\Shipping\Model\Shipping;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result as RateResult;
use PriyankJivani\ShippingRules\Model\Service\RuleProcessor;

class ShippingPlugin
{
    private RuleProcessor $ruleProcessor;

    public function __construct(RuleProcessor $ruleProcessor)
    {
        $this->ruleProcessor = $ruleProcessor;
    }

    public function afterCollectRates(
        Shipping $subject,
        Shipping $result,
        RateRequest $request
    ): Shipping {
        $rateResult = $result->getResult();
        if (!$rateResult instanceof RateResult) {
            return $result;
        }

        $allRates = $rateResult->getAllRates();
        if (empty($allRates)) {
            return $result;
        }

        // Process rates through shipping rules engine
        $this->ruleProcessor->processRates($allRates, $request);

        return $result;
    }
}
