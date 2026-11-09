<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Model\Service;

use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class RuleEvaluator
{
    private TimezoneInterface $timezone;
    private State $appState;

    public function __construct(
        TimezoneInterface $timezone,
        State $appState
    ) {
        $this->timezone = $timezone;
        $this->appState = $appState;
    }

    public function isRuleApplicable(
        RuleInterface $rule,
        RateRequest $request,
        string $carrierCode,
        string $methodCode
    ): bool {
        if (!$rule->getIsActive()) {
            return false;
        }

        // 1. Admin Area Check
        if ($this->isAdminArea() && !$rule->getApplyInAdmin()) {
            return false;
        }

        // 2. Carrier & Method Matching
        if (!$this->matchCarrierAndMethod($rule, $carrierCode, $methodCode)) {
            return false;
        }

        // 3. Store View Matching
        $storeId = (int)$request->getStoreId();
        $ruleStores = $rule->getStoreIds();
        if (!empty($ruleStores) && !in_array(0, $ruleStores) && !in_array($storeId, $ruleStores)) {
            return false;
        }

        // 4. Date & Schedule Matching
        if (!$this->matchSchedule($rule, $storeId)) {
            return false;
        }

        // 5. Quote Context Checks
        $items = $request->getAllItems();
        $firstItem = !empty($items) ? reset($items) : null;
        $quote = $firstItem ? $firstItem->getQuote() : null;

        if ($quote) {
            // Customer Group Matching
            $customerGroupId = (int)$quote->getCustomerGroupId();
            $ruleGroups = $rule->getCustomerGroupIds();
            if (!empty($ruleGroups) && !in_array($customerGroupId, $ruleGroups)) {
                return false;
            }

            // Coupon Code Matching
            if (!$this->matchCoupon($rule, (string)$quote->getCouponCode())) {
                return false;
            }

            // Backorder Matching
            if (!$this->matchBackorders($rule, $items)) {
                return false;
            }

            // Condition Tree Evaluation
            $address = $quote->getShippingAddress();
            if ($address && !$rule->validate($address)) {
                return false;
            }
        }

        return true;
    }

    private function matchCarrierAndMethod(RuleInterface $rule, string $carrierCode, string $methodCode): bool
    {
        $carriersStr = (string)$rule->getCarriers();
        $methodsStr = (string)$rule->getMethods();

        // If specific carriers configured
        if ($carriersStr !== '') {
            $applicableCarriers = array_map('trim', explode(',', $carriersStr));
            if (!in_array($carrierCode, $applicableCarriers, true)) {
                return false;
            }
        }

        // If specific methods configured (carrier_method format e.g. flatrate_flatrate)
        if ($methodsStr !== '') {
            $applicableMethods = array_map('trim', explode(',', $methodsStr));
            $fullMethodCode = $carrierCode . '_' . $methodCode;
            if (!in_array($fullMethodCode, $applicableMethods, true) && !in_array($methodCode, $applicableMethods, true)) {
                return false;
            }
        }

        return true;
    }

    private function matchSchedule(RuleInterface $rule, int $storeId): bool
    {
        $now = $this->timezone->date(); // In store configured timezone

        // Date range
        $todayStr = $now->format('Y-m-d');
        if ($rule->getFromDate() && $todayStr < $rule->getFromDate()) {
            return false;
        }
        if ($rule->getToDate() && $todayStr > $rule->getToDate()) {
            return false;
        }

        // Day of week (0=Sunday ... 6=Saturday)
        $dayOfWeek = (int)$now->format('w');
        $daysStr = $rule->getDaysOfWeek();
        if ($daysStr !== null && $daysStr !== '') {
            $activeDays = array_map('intval', explode(',', $daysStr));
            if (!in_array($dayOfWeek, $activeDays, true)) {
                return false;
            }
        }

        // Time From / To
        $timeFrom = $rule->getTimeFrom();
        $timeTo = $rule->getTimeTo();
        if ($timeFrom && $timeTo) {
            $currentHi = $now->format('H:i');
            if ($timeFrom <= $timeTo) {
                // Normal same-day window e.g. 09:00 to 18:00
                if ($currentHi < $timeFrom || $currentHi > $timeTo) {
                    return false;
                }
            } else {
                // Crossing midnight e.g. 22:00 to 04:00
                if ($currentHi < $timeFrom && $currentHi > $timeTo) {
                    return false;
                }
            }
        }

        return true;
    }

    private function matchCoupon(RuleInterface $rule, string $quoteCoupon): bool
    {
        $type = $rule->getCouponType();
        if ($type === RuleInterface::COUPON_NONE) {
            return true;
        }

        $ruleCoupon = strtoupper(trim((string)$rule->getCouponCode()));
        $appliedCoupon = strtoupper(trim($quoteCoupon));

        if ($type === RuleInterface::COUPON_SPECIFIC) {
            return $appliedCoupon !== '' && $appliedCoupon === $ruleCoupon;
        }

        if ($type === RuleInterface::COUPON_EXCLUDE) {
            return $appliedCoupon === '' || $appliedCoupon !== $ruleCoupon;
        }

        return true;
    }

    private function matchBackorders(RuleInterface $rule, array $items): bool
    {
        $mode = $rule->getBackorderMode();
        if ($mode === RuleInterface::BACKORDER_ALL) {
            return true;
        }

        $hasBackorder = false;
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            if ($item->getBackorders() > 0) {
                $hasBackorder = true;
                break;
            }
        }

        if ($mode === RuleInterface::BACKORDER_ONLY) {
            return $hasBackorder;
        }

        if ($mode === RuleInterface::BACKORDER_EXCLUDE) {
            return !$hasBackorder;
        }

        return true;
    }

    private function isAdminArea(): bool
    {
        try {
            return $this->appState->getAreaCode() === Area::AREA_ADMINHTML;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
