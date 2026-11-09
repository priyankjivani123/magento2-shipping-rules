<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use PriyankJivani\ShippingRules\Model\Service\RuleEvaluator;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class RuleEvaluatorTest extends TestCase
{
    private $timezoneMock;
    private $appStateMock;
    private RuleEvaluator $evaluator;

    protected function setUp(): void
    {
        $this->timezoneMock = $this->createMock(TimezoneInterface::class);
        $this->appStateMock = $this->createMock(State::class);
        $this->appStateMock->method('getAreaCode')->willReturn(Area::AREA_FRONTEND);

        $now = new \DateTimeImmutable('2026-11-09 14:30:00'); // Monday, 14:30
        $this->timezoneMock->method('date')->willReturn($now);

        $this->evaluator = new RuleEvaluator($this->timezoneMock, $this->appStateMock);
    }

    private function createRuleMock(array $data): RuleInterface
    {
        $rule = $this->createMock(RuleInterface::class);
        $rule->method('getIsActive')->willReturn($data['is_active'] ?? true);
        $rule->method('getApplyInAdmin')->willReturn($data['apply_in_admin'] ?? true);
        $rule->method('getCarriers')->willReturn($data['carriers'] ?? '');
        $rule->method('getMethods')->willReturn($data['methods'] ?? '');
        $rule->method('getStoreIds')->willReturn($data['store_ids'] ?? [0]);
        $rule->method('getFromDate')->willReturn($data['from_date'] ?? null);
        $rule->method('getToDate')->willReturn($data['to_date'] ?? null);
        $rule->method('getDaysOfWeek')->willReturn($data['days_of_week'] ?? null);
        $rule->method('getTimeFrom')->willReturn($data['time_from'] ?? null);
        $rule->method('getTimeTo')->willReturn($data['time_to'] ?? null);
        $rule->method('getCouponType')->willReturn((int)($data['coupon_type'] ?? RuleInterface::COUPON_NONE));
        $rule->method('getCouponCode')->willReturn($data['coupon_code'] ?? null);
        $rule->method('getBackorderMode')->willReturn((int)($data['backorder_mode'] ?? RuleInterface::BACKORDER_ALL));
        $rule->method('getCustomerGroupIds')->willReturn($data['customer_group_ids'] ?? [0, 1, 2, 3]);

        return $rule;
    }

    private function createRateRequest(int $storeId = 1): RateRequest
    {
        $request = new RateRequest();
        $request->setStoreId($storeId);
        $request->setAllItems([]);
        return $request;
    }

    public function testInactiveRuleIsSkipped(): void
    {
        $rule = $this->createRuleMock(['is_active' => false]);
        $request = $this->createRateRequest();

        $this->assertFalse($this->evaluator->isRuleApplicable($rule, $request, 'flatrate', 'flatrate'));
    }

    public function testCarrierAndMethodMatching(): void
    {
        // Rule specific to 'ups' carrier
        $ruleUPS = $this->createRuleMock(['carriers' => 'ups,fedex']);
        $request = $this->createRateRequest();

        $this->assertTrue($this->evaluator->isRuleApplicable($ruleUPS, $request, 'ups', 'express'));
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleUPS, $request, 'flatrate', 'flatrate'));

        // Rule specific to exact method
        $ruleMethod = $this->createRuleMock(['methods' => 'flatrate_flatrate']);
        $this->assertTrue($this->evaluator->isRuleApplicable($ruleMethod, $request, 'flatrate', 'flatrate'));
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleMethod, $request, 'flatrate', 'other_method'));
    }

    public function testStoreViewFiltering(): void
    {
        // Store 1 allowed, not store 2
        $ruleStore1 = $this->createRuleMock(['store_ids' => [1]]);
        $requestStore1 = $this->createRateRequest(1);
        $requestStore2 = $this->createRateRequest(2);

        $this->assertTrue($this->evaluator->isRuleApplicable($ruleStore1, $requestStore1, 'flatrate', 'flatrate'));
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleStore1, $requestStore2, 'flatrate', 'flatrate'));

        // All store views (0)
        $ruleAllStores = $this->createRuleMock(['store_ids' => [0]]);
        $this->assertTrue($this->evaluator->isRuleApplicable($ruleAllStores, $requestStore2, 'flatrate', 'flatrate'));
    }

    public function testDateRangeValidation(): void
    {
        // Active range
        $ruleActive = $this->createRuleMock(['from_date' => '2026-11-01', 'to_date' => '2026-11-30']);
        $request = $this->createRateRequest();
        $this->assertTrue($this->evaluator->isRuleApplicable($ruleActive, $request, 'flatrate', 'flatrate'));

        // Expired range
        $ruleExpired = $this->createRuleMock(['from_date' => '2026-10-01', 'to_date' => '2026-10-31']);
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleExpired, $request, 'flatrate', 'flatrate'));

        // Future range
        $ruleFuture = $this->createRuleMock(['from_date' => '2026-12-01', 'to_date' => '2026-12-31']);
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleFuture, $request, 'flatrate', 'flatrate'));
    }

    public function testTimeWindowSameDayAndCrossingMidnight(): void
    {
        $request = $this->createRateRequest();

        // 14:30 is between 09:00 and 18:00 -> TRUE
        $ruleDay = $this->createRuleMock(['time_from' => '09:00', 'time_to' => '18:00']);
        $this->assertTrue($this->evaluator->isRuleApplicable($ruleDay, $request, 'flatrate', 'flatrate'));

        // 14:30 is outside 18:00 to 22:00 -> FALSE
        $ruleEvening = $this->createRuleMock(['time_from' => '18:00', 'time_to' => '22:00']);
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleEvening, $request, 'flatrate', 'flatrate'));

        // Crossing midnight: 22:00 to 06:00 (14:30 is outside) -> FALSE
        $ruleNight = $this->createRuleMock(['time_from' => '22:00', 'time_to' => '06:00']);
        $this->assertFalse($this->evaluator->isRuleApplicable($ruleNight, $request, 'flatrate', 'flatrate'));
    }
}
