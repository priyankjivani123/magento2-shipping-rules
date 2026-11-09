<?php
declare(strict_types=1);

namespace PriyankJivani\ShippingRules\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use PriyankJivani\ShippingRules\Model\Service\RateCalculator;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class RateCalculatorTest extends TestCase
{
    private RateCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new RateCalculator();
    }

    private function createMockRule(array $data): RuleInterface
    {
        $rule = $this->createMock(RuleInterface::class);
        $rule->method('getRuleId')->willReturn($data['rule_id'] ?? 1);
        $rule->method('getCalculationType')->willReturn($data['calculation_type'] ?? RuleInterface::CALC_REPLACE);
        $rule->method('getCalcBaseRate')->willReturn((float)($data['calc_base_rate'] ?? 0));
        $rule->method('getCalcPerProduct')->willReturn((float)($data['calc_per_product'] ?? 0));
        $rule->method('getCalcPerWeight')->willReturn((float)($data['calc_per_weight'] ?? 0));
        $rule->method('getCalcPercentProduct')->willReturn((float)($data['calc_percent_product'] ?? 0));
        $rule->method('getCalcHandlingPercent')->willReturn((float)($data['calc_handling_percent'] ?? 0));
        $rule->method('getMinRateChange')->willReturn($data['min_rate_change'] ?? null);
        $rule->method('getMaxRateChange')->willReturn($data['max_rate_change'] ?? null);
        $rule->method('getMinTotalRate')->willReturn($data['min_total_rate'] ?? null);
        $rule->method('getMaxTotalRate')->willReturn($data['max_total_rate'] ?? null);
        $rule->method('getCustomCarrierLabel')->willReturn($data['custom_carrier_label'] ?? null);
        $rule->method('getCustomMethodLabel')->willReturn($data['custom_method_label'] ?? null);

        return $rule;
    }

    private function createMockRateRequest(float $packageQty = 2.0, float $packageWeight = 10.0, float $subtotal = 100.0): RateRequest
    {
        $item1 = new \Magento\Framework\DataObject([
            'parent_item' => null,
            'qty' => 2.0,
            'weight' => 10.0,
            'base_row_total' => 100.0
        ]);

        $request = new RateRequest();
        $request->setAllItems([$item1]);
        $request->setPackageQty($packageQty);
        $request->setPackageWeight($packageWeight);

        return $request;
    }

    public function testReplaceCalculation(): void
    {
        $rule = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_REPLACE,
            'calc_base_rate' => 15.00
        ]);
        $request = $this->createMockRateRequest();

        $result = $this->calculator->calculate($rule, 50.00, $request);

        $this->assertEquals(15.00, $result['final_price']);
        $this->assertEquals(50.00, $result['original_price']);
    }

    public function testSurchargeCalculation(): void
    {
        $rule = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_SURCHARGE,
            'calc_base_rate' => 10.00,
            'calc_per_product' => 2.50, // 2 items * 2.50 = 5.00
            'calc_per_weight' => 1.00,   // (2 qty * 10.0 lbs = 20 lbs) * 1.00 = 20.00
            'calc_handling_percent' => 10.0 // 10% of 20.00 = 2.00
        ]);
        // Total surcharge = 10 + 5 + 20 + 2 = 37.00
        $request = $this->createMockRateRequest(2.0, 20.0, 100.0);

        $result = $this->calculator->calculate($rule, 20.00, $request);

        $this->assertEquals(57.00, $result['final_price']);
    }

    public function testDiscountCalculationDoesNotGoBelowZero(): void
    {
        $rule = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_DISCOUNT,
            'calc_base_rate' => 100.00 // Discount exceeds original rate
        ]);
        $request = $this->createMockRateRequest();

        $result = $this->calculator->calculate($rule, 15.00, $request);

        $this->assertEquals(0.00, $result['final_price'], 'Rate must never drop below $0.00');
        $this->assertEquals(15.00, $result['discount_amount']);
    }

    public function testDiscountCalculationStandard(): void
    {
        $rule = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_DISCOUNT,
            'calc_base_rate' => 5.00
        ]);
        $request = $this->createMockRateRequest();

        $result = $this->calculator->calculate($rule, 20.00, $request);

        $this->assertEquals(15.00, $result['final_price']);
        $this->assertEquals(5.00, $result['discount_amount']);
    }

    public function testMinMaxRateChangeClamps(): void
    {
        // Case 1: Adjustment is below minimum rate change
        $ruleMin = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_SURCHARGE,
            'calc_base_rate' => 2.00,
            'min_rate_change' => 5.00
        ]);
        $request = $this->createMockRateRequest();
        $resultMin = $this->calculator->calculate($ruleMin, 10.00, $request);
        $this->assertEquals(15.00, $resultMin['final_price']); // 10 + min(5)

        // Case 2: Adjustment exceeds maximum rate change
        $ruleMax = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_SURCHARGE,
            'calc_base_rate' => 50.00,
            'max_rate_change' => 20.00
        ]);
        $resultMax = $this->calculator->calculate($ruleMax, 10.00, $request);
        $this->assertEquals(30.00, $resultMax['final_price']); // 10 + max(20)
    }

    public function testMinMaxTotalRateClamps(): void
    {
        // Enforce maximum total cap
        $ruleCap = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_SURCHARGE,
            'calc_base_rate' => 100.00,
            'max_total_rate' => 45.00
        ]);
        $request = $this->createMockRateRequest();
        $resultCap = $this->calculator->calculate($ruleCap, 10.00, $request);
        $this->assertEquals(45.00, $resultCap['final_price']);

        // Enforce minimum total floor
        $ruleFloor = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_DISCOUNT,
            'calc_base_rate' => 20.00,
            'min_total_rate' => 8.00
        ]);
        $resultFloor = $this->calculator->calculate($ruleFloor, 25.00, $request);
        $this->assertEquals(8.00, $resultFloor['final_price']); // 25 - 20 = 5 -> clamped to min floor 8.00
    }

    public function testCustomLabelsPassedCorrectly(): void
    {
        $rule = $this->createMockRule([
            'calculation_type' => RuleInterface::CALC_REPLACE,
            'calc_base_rate' => 0.00,
            'custom_carrier_label' => 'VIP Express Carrier',
            'custom_method_label' => 'Complimentary Priority Freight'
        ]);
        $request = $this->createMockRateRequest();

        $result = $this->calculator->calculate($rule, 15.00, $request);

        $this->assertEquals('VIP Express Carrier', $result['custom_carrier_label']);
        $this->assertEquals('Complimentary Priority Freight', $result['custom_method_label']);
    }
}
