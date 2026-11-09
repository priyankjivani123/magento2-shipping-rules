<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Service;

use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

class RateCalculator
{
    public function calculate(
        RuleInterface $rule,
        float $originalRate,
        RateRequest $request
    ): array {
        $calcType = $rule->getCalculationType();
        $items = (array)($request->getAllItems() ?? []);

        // 1. Determine applicable items and metrics (Whole cart vs Product matching)
        $applicableMetrics = $this->extractMetrics($rule, $items, $calcType === RuleInterface::CALC_PARTIAL_REPLACE);

        // 2. Compute calculated rule component
        $baseFee = (float)$rule->getCalcBaseRate();
        $perProductFee = (float)$rule->getCalcPerProduct() * $applicableMetrics['qty'];
        $perWeightFee = (float)$rule->getCalcPerWeight() * $applicableMetrics['weight'];
        $percentProductFee = ((float)$rule->getCalcPercentProduct() / 100.0) * $applicableMetrics['subtotal'];
        $handlingFee = ((float)$rule->getCalcHandlingPercent() / 100.0) * $originalRate;

        $rawAdjustment = $baseFee + $perProductFee + $perWeightFee + $percentProductFee + $handlingFee;

        // 3. Apply min/max adjustment clamps if configured
        if ($rule->getMinRateChange() !== null && $rawAdjustment < (float)$rule->getMinRateChange()) {
            $rawAdjustment = (float)$rule->getMinRateChange();
        }
        if ($rule->getMaxRateChange() !== null && $rawAdjustment > (float)$rule->getMaxRateChange()) {
            $rawAdjustment = (float)$rule->getMaxRateChange();
        }

        // 4. Calculate Final Shipping Amount based on calculation type
        $discountAmount = 0.0;
        $finalRate = $originalRate;

        switch ($calcType) {
            case RuleInterface::CALC_REPLACE:
                $finalRate = $rawAdjustment;
                break;

            case RuleInterface::CALC_SURCHARGE:
                $finalRate = $originalRate + $rawAdjustment;
                break;

            case RuleInterface::CALC_DISCOUNT:
                $discountAmount = min($originalRate, max(0.0, $rawAdjustment));
                $finalRate = max(0.0, $originalRate - $discountAmount);
                break;

            case RuleInterface::CALC_PARTIAL_REPLACE:
                // Mixed Cart: proportional original shipping for non-matching items + rawAdjustment for matching items
                $totalCartQty = max(1.0, (float)$request->getPackageQty());
                $matchingQty = (float)$applicableMetrics['qty'];
                $nonMatchingRatio = max(0.0, ($totalCartQty - $matchingQty) / $totalCartQty);

                $proportionalOriginal = $originalRate * $nonMatchingRatio;
                $finalRate = $proportionalOriginal + $rawAdjustment;
                break;
        }

        // 5. Apply min/max total rate clamps
        if ($rule->getMinTotalRate() !== null && $finalRate < (float)$rule->getMinTotalRate()) {
            $finalRate = (float)$rule->getMinTotalRate();
        }
        if ($rule->getMaxTotalRate() !== null && $finalRate > (float)$rule->getMaxTotalRate()) {
            $finalRate = (float)$rule->getMaxTotalRate();
        }

        // Safety safeguard: Never negative rate
        $finalRate = max(0.0, round($finalRate, 2));

        return [
            'original_price' => round($originalRate, 2),
            'final_price' => $finalRate,
            'discount_amount' => round($discountAmount, 2),
            'custom_carrier_label' => $rule->getCustomCarrierLabel(),
            'custom_method_label' => $rule->getCustomMethodLabel(),
            'rule_id' => (int)$rule->getRuleId()
        ];
    }

    private function extractMetrics(RuleInterface $rule, array $items, bool $filterByProduct): array
    {
        $qty = 0.0;
        $weight = 0.0;
        $subtotal = 0.0;

        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }

            if ($filterByProduct) {
                // If rule has actions/subselection conditions, validate item
                $actions = $rule->getActions();
                if ($actions && $item instanceof \Magento\Framework\Model\AbstractModel && !$actions->validate($item)) {
                    continue;
                }
            }

            $qty += (float)$item->getQty();
            $weight += (float)($item->getWeight() * $item->getQty());
            $subtotal += (float)$item->getBaseRowTotal();
        }

        return [
            'qty' => $qty,
            'weight' => $weight,
            'subtotal' => $subtotal
        ];
    }
}
