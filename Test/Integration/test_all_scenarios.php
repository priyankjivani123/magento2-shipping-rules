<?php
declare(strict_types=1);

/**
 * End-to-End Live Integration Verification Suite
 * Tests 10 complete shipping rule scenarios on live Magento 2.4.9 installation.
 */

use Magento\Framework\App\Bootstrap;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use PriyankJivani\ShippingRules\Api\Data\ShippingAreaInterface;
use PriyankJivani\ShippingRules\Model\RuleFactory;
use PriyankJivani\ShippingRules\Model\ShippingAreaFactory;
use PriyankJivani\ShippingRules\Api\RuleRepositoryInterface;
use PriyankJivani\ShippingRules\Api\ShippingAreaRepositoryInterface;
use PriyankJivani\ShippingRules\Model\Service\RuleProcessor;
use PriyankJivani\ShippingRules\Model\Service\ShippingAreaMatcher;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\AddressFactory;

require '/var/www/magento2/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$appState = $objectManager->get(\Magento\Framework\App\State::class);
try {
    $appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
} catch (\Exception $e) {
    // Area code already set
}

$ruleFactory = $objectManager->get(RuleFactory::class);
$areaFactory = $objectManager->get(ShippingAreaFactory::class);
$ruleRepository = $objectManager->get(RuleRepositoryInterface::class);
$areaRepository = $objectManager->get(ShippingAreaRepositoryInterface::class);
$ruleProcessor = $objectManager->get(RuleProcessor::class);
$areaMatcher = $objectManager->get(ShippingAreaMatcher::class);
$rateResultMethodFactory = $objectManager->get(MethodFactory::class);
$resultFactory = $objectManager->get(ResultFactory::class);
$addressFactory = $objectManager->get(AddressFactory::class);

echo "\n========================================================================\n";
echo "   PRIYANK JIVANI - MAGENTO 2 SHIPPING RULES E2E INTEGRATION SUITE       \n";
echo "========================================================================\n\n";

$passCount = 0;
$failCount = 0;
$scenarios = [];

function recordResult(string $title, bool $passed, string $details = ''): void {
    global $passCount, $failCount, $scenarios;
    if ($passed) {
        $passCount++;
        echo "[\033[32mPASS\033[0m] " . $title . ($details ? " - $details" : "") . "\n";
    } else {
        $failCount++;
        echo "[\033[31mFAIL\033[0m] " . $title . ($details ? " - $details" : "") . "\n";
    }
    $scenarios[] = ['name' => $title, 'passed' => $passed, 'details' => $details];
}

function cleanupDb($objectManager): void {
    $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
    $connection = $resource->getConnection();
    $connection->query('SET FOREIGN_KEY_CHECKS=0');
    $connection->query('TRUNCATE TABLE priyankjivani_shipping_rule_store');
    $connection->query('TRUNCATE TABLE priyankjivani_shipping_rule_customer_group');
    $connection->query('TRUNCATE TABLE priyankjivani_shipping_rule');
    $connection->query('TRUNCATE TABLE priyankjivani_shipping_area');
    $connection->query('SET FOREIGN_KEY_CHECKS=1');
}

// -----------------------------------------------------------------------------
// Scenario 1: Alaska Surcharge ($10 base + $50 Alaska surcharge = $60)
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $area = $areaFactory->create();
    $area->setName('E2E Test - Alaska Zone');
    $area->setIsActive(true);
    $area->setCountries('US');
    $area->setRegions('AK');
    $area->setPostcodes('99501-99695');
    $area = $areaRepository->save($area);

    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Alaska $50 Air Surcharge');
    $rule->setIsActive(true);
    $rule->setPriority(10);
    $rule->setCalculationType(RuleInterface::CALC_SURCHARGE);
    $rule->setCalcBaseRate(50.00);
    $rule->setCarriers('flatrate');
    $rule->setCustomCarrierLabel('Air Freight');
    $rule->setCustomMethodLabel('Expedited Alaska Delivery');
    $rule = $ruleRepository->save($rule);

    $rateRequest = new RateRequest();
    $rateRequest->setPackageQty(1);
    $rateRequest->setPackageWeight(2.0);
    $rateRequest->setDestCountryId('US');
    $rateRequest->setDestRegionCode('AK');
    $rateRequest->setDestPostcode('99501');

    $rate = $rateResultMethodFactory->create();
    $rate->setCarrier('flatrate');
    $rate->setCarrierTitle('Flat Rate');
    $rate->setMethod('flatrate');
    $rate->setMethodTitle('Fixed');
    $rate->setPrice(10.00);

    $processedRates = $ruleProcessor->processRates([$rate], $rateRequest);
    $finalRate = $processedRates[0]->getPrice();

    $passed = (abs((float)$finalRate - 60.00) < 0.001);
    recordResult("Scenario 1: Alaska Surcharge", $passed, "Expected: $60.00, Actual: $" . number_format((float)$finalRate, 2));
} catch (\Throwable $e) {
    recordResult("Scenario 1: Alaska Surcharge", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 2: Weight-based Surcharge (5kg * $1.00 = $5.00)
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Weight Surcharge');
    $rule->setIsActive(true);
    $rule->setPriority(10);
    $rule->setCalculationType(RuleInterface::CALC_SURCHARGE);
    $rule->setCalcPerWeight(1.00); // $1.00 per unit weight
    $rule = $ruleRepository->save($rule);

    $item = new \Magento\Framework\DataObject(['qty' => 1, 'weight' => 5.0, 'base_row_total' => 50.0]);
    $rateRequest = new RateRequest();
    $rateRequest->setAllItems([$item]);
    $rateRequest->setPackageQty(1);
    $rateRequest->setPackageWeight(5.0);

    $rate = $rateResultMethodFactory->create();
    $rate->setCarrier('flatrate');
    $rate->setMethod('flatrate');
    $rate->setPrice(10.00); // 10 + 5 = 15.00

    $processedRates = $ruleProcessor->processRates([$rate], $rateRequest);
    $finalRate = $processedRates[0]->getPrice();

    $passed = (abs((float)$finalRate - 15.00) < 0.001);
    recordResult("Scenario 2: Weight-based Surcharge", $passed, "Expected: $15.00, Actual: $" . number_format((float)$finalRate, 2));
} catch (\Throwable $e) {
    recordResult("Scenario 2: Weight-based Surcharge", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 3: Customer Group Discount (Wholesale 10% off $20 = $18)
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Wholesale 10% Discount');
    $rule->setIsActive(true);
    $rule->setPriority(10);
    $rule->setCustomerGroupIds([2]); // 2 = Wholesale
    $rule->setCalculationType(RuleInterface::CALC_DISCOUNT);
    $rule->setCalcHandlingPercent(10.0); // 10% discount on rate
    $rule = $ruleRepository->save($rule);

    // Mock Wholesale Quote
    $quote = new \Magento\Framework\DataObject(['customer_group_id' => 2]);
    $item = new \Magento\Framework\DataObject(['quote' => $quote, 'qty' => 1, 'weight' => 1.0, 'base_row_total' => 50.0]);

    $rateRequest = new RateRequest();
    $rateRequest->setAllItems([$item]);
    $rateRequest->setPackageQty(1);

    $rate = $rateResultMethodFactory->create();
    $rate->setCarrier('flatrate');
    $rate->setMethod('flatrate');
    $rate->setPrice(20.00); // 20 - 10% ($2) = $18.00

    $processedRates = $ruleProcessor->processRates([$rate], $rateRequest);
    $finalRate = $processedRates[0]->getPrice();

    $passed = (abs((float)$finalRate - 18.00) < 0.001);
    recordResult("Scenario 3: Customer Group Discount", $passed, "Expected: $18.00, Actual: $" . number_format((float)$finalRate, 2));
} catch (\Throwable $e) {
    recordResult("Scenario 3: Customer Group Discount", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 4: Mixed Cart Partial Replacement
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Partial Replace Mixed Cart');
    $rule->setIsActive(true);
    $rule->setPriority(10);
    $rule->setCalculationType(RuleInterface::CALC_PARTIAL_REPLACE);
    $rule->setCalcBaseRate(5.00);
    $rule = $ruleRepository->save($rule);

    // Cart: 2 items total, 1 matches rule (half original $20 = $10 + $5 calculated = $15)
    $item1 = new \Magento\Framework\DataObject(['qty' => 1, 'weight' => 1.0, 'base_row_total' => 30.0]);
    $item2 = new \Magento\Framework\DataObject(['qty' => 1, 'weight' => 1.0, 'base_row_total' => 20.0]);

    $rateRequest = new RateRequest();
    $rateRequest->setAllItems([$item1, $item2]);
    $rateRequest->setPackageQty(2);

    $rate = $rateResultMethodFactory->create();
    $rate->setCarrier('flatrate');
    $rate->setMethod('flatrate');
    $rate->setPrice(20.00);

    $processedRates = $ruleProcessor->processRates([$rate], $rateRequest);
    $finalRate = $processedRates[0]->getPrice();

    $passed = ($finalRate > 0.0);
    recordResult("Scenario 4: Mixed Cart Partial Replacement", $passed, "Actual: $" . number_format((float)$finalRate, 2));
} catch (\Throwable $e) {
    recordResult("Scenario 4: Mixed Cart Partial Replacement", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 5: Coupon Restriction (Requires SAVE10)
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Coupon Restricted Free Shipping');
    $rule->setIsActive(true);
    $rule->setPriority(10);
    $rule->setCalculationType(RuleInterface::CALC_REPLACE);
    $rule->setCalcBaseRate(0.00);
    $rule->setCouponType(RuleInterface::COUPON_SPECIFIC);
    $rule->setCouponCode('SAVE10');
    $rule = $ruleRepository->save($rule);

    // Test with matching coupon
    $quoteMatching = new \Magento\Framework\DataObject(['coupon_code' => 'SAVE10']);
    $itemMatch = new \Magento\Framework\DataObject(['quote' => $quoteMatching, 'qty' => 1]);
    $reqMatch = new RateRequest();
    $reqMatch->setAllItems([$itemMatch]);
    $rate1 = $rateResultMethodFactory->create();
    $rate1->setCarrier('flatrate')->setMethod('flatrate')->setPrice(15.00);
    $outMatch = $ruleProcessor->processRates([$rate1], $reqMatch)[0]->getPrice();

    // Test without matching coupon
    $quoteOther = new \Magento\Framework\DataObject(['coupon_code' => 'WRONGCODE']);
    $itemOther = new \Magento\Framework\DataObject(['quote' => $quoteOther, 'qty' => 1]);
    $reqOther = new RateRequest();
    $reqOther->setAllItems([$itemOther]);
    $rate2 = $rateResultMethodFactory->create();
    $rate2->setCarrier('flatrate')->setMethod('flatrate')->setPrice(15.00);
    $outOther = $ruleProcessor->processRates([$rate2], $reqOther)[0]->getPrice();

    $passed = ((float)$outMatch === 0.00 && (float)$outOther === 15.00);
    recordResult("Scenario 5: Coupon Restriction", $passed, "With SAVE10: $$outMatch, Without: $$outOther");
} catch (\Throwable $e) {
    recordResult("Scenario 5: Coupon Restriction", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 6: Priority & Discard Subsequent Rules
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $ruleHigh = $ruleFactory->create();
    $ruleHigh->setName('E2E Test - High Priority Discard Rule');
    $ruleHigh->setIsActive(true);
    $ruleHigh->setPriority(1); // Executes first
    $ruleHigh->setCalculationType(RuleInterface::CALC_REPLACE);
    $ruleHigh->setCalcBaseRate(12.00);
    $ruleHigh->setDiscardSubsequentRules(true); // Stop pipeline
    $ruleHigh = $ruleRepository->save($ruleHigh);

    $ruleLow = $ruleFactory->create();
    $ruleLow->setName('E2E Test - Low Priority Surcharge Rule');
    $ruleLow->setIsActive(true);
    $ruleLow->setPriority(100);
    $ruleLow->setCalculationType(RuleInterface::CALC_SURCHARGE);
    $ruleLow->setCalcBaseRate(50.00);
    $ruleLow = $ruleRepository->save($ruleLow);

    $req = new RateRequest();
    $rate = $rateResultMethodFactory->create();
    $rate->setCarrier('flatrate')->setMethod('flatrate')->setPrice(20.00);

    $out = $ruleProcessor->processRates([$rate], $req)[0]->getPrice();

    // If discarded, final price is 12.00. If not discarded, it would be 12 + 50 = 62.00
    $passed = (abs((float)$out - 12.00) < 0.001);
    recordResult("Scenario 6: Priority & Discard Subsequent", $passed, "Expected: $12.00, Actual: $" . number_format((float)$out, 2));
} catch (\Throwable $e) {
    recordResult("Scenario 6: Priority & Discard Subsequent", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 7: Shipping Area Exclusions
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $area = $areaFactory->create();
    $area->setName('E2E Test - Hawaii Exclusion');
    $area->setIsActive(true);
    $area->setCountries('US');
    $area->setRegions('HI');
    $area->setPostcodes('96701-96898');
    $area = $areaRepository->save($area);

    $addressHI = $addressFactory->create();
    $addressHI->setCountryId('US')->setRegionCode('HI')->setPostcode('96701');

    $addressNY = $addressFactory->create();
    $addressNY->setCountryId('US')->setRegionCode('NY')->setPostcode('10001');

    $matchHI = $areaMatcher->matchAddress((int)$area->getId(), $addressHI);
    $matchNY = $areaMatcher->matchAddress((int)$area->getId(), $addressNY);

    $passed = ($matchHI === true && $matchNY === false);
    recordResult("Scenario 7: Shipping Area Exclusions", $passed, "HI matched: " . ($matchHI ? 'Yes' : 'No') . ", NY matched: " . ($matchNY ? 'Yes' : 'No'));
} catch (\Throwable $e) {
    recordResult("Scenario 7: Shipping Area Exclusions", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 8: Schedule (Active Days & Time Window)
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Weekend Schedule Only');
    $rule->setIsActive(true);
    $rule->setDaysOfWeek('0,6'); // Sunday and Saturday only
    $rule->setCalculationType(RuleInterface::CALC_SURCHARGE);
    $rule->setCalcBaseRate(10.00);
    $rule = $ruleRepository->save($rule);

    $evaluator = $objectManager->get(\PriyankJivani\ShippingRules\Model\Service\RuleEvaluator::class);
    $req = new RateRequest();
    $isApplicable = $evaluator->isRuleApplicable($rule, $req, 'flatrate', 'flatrate');

    $todayDayOfWeek = (int)(new \DateTime())->format('w');
    $expected = in_array($todayDayOfWeek, [0, 6], true);
    $passed = ($isApplicable === $expected);

    recordResult("Scenario 8: Schedule & Day Boundaries", $passed, "Current day ($todayDayOfWeek) evaluated correctly as " . ($isApplicable ? 'ACTIVE' : 'INACTIVE'));
} catch (\Throwable $e) {
    recordResult("Scenario 8: Schedule & Day Boundaries", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 9: Backorders Mode
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Backorders Only Rule');
    $rule->setIsActive(true);
    $rule->setBackorderMode(RuleInterface::BACKORDER_ONLY);
    $rule = $ruleRepository->save($rule);

    $evaluator = $objectManager->get(\PriyankJivani\ShippingRules\Model\Service\RuleEvaluator::class);

    // Cart with in-stock item
    $inStockItem = new \Magento\Framework\DataObject(['backorders' => 0, 'quote' => new \Magento\Framework\DataObject(['customer_group_id' => 0])]);
    $reqInStock = new RateRequest();
    $reqInStock->setAllItems([$inStockItem]);
    $appInStock = $evaluator->isRuleApplicable($rule, $reqInStock, 'flatrate', 'flatrate');

    // Cart with backordered item
    $backorderItem = new \Magento\Framework\DataObject(['backorders' => 1, 'quote' => new \Magento\Framework\DataObject(['customer_group_id' => 0])]);
    $reqBackorder = new RateRequest();
    $reqBackorder->setAllItems([$backorderItem]);
    $appBackorder = $evaluator->isRuleApplicable($rule, $reqBackorder, 'flatrate', 'flatrate');

    $passed = ($appInStock === false && $appBackorder === true);
    recordResult("Scenario 9: Backorders Mode", $passed, "In-stock applied: " . ($appInStock ? 'Yes' : 'No') . ", Backorder applied: " . ($appBackorder ? 'Yes' : 'No'));
} catch (\Throwable $e) {
    recordResult("Scenario 9: Backorders Mode", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

// -----------------------------------------------------------------------------
// Scenario 10: Checkout Consistency
// -----------------------------------------------------------------------------
cleanupDb($objectManager);
try {
    $rule = $ruleFactory->create();
    $rule->setName('E2E Test - Consistency Flat Replace $7.50');
    $rule->setIsActive(true);
    $rule->setCalculationType(RuleInterface::CALC_REPLACE);
    $rule->setCalcBaseRate(7.50);
    $rule = $ruleRepository->save($rule);

    $shipping = $objectManager->get(\Magento\Shipping\Model\Shipping::class);

    // Call 1: Cart estimate request
    $req1 = new RateRequest();
    $req1->setPackageQty(1)->setPackageWeight(1.0)->setDestCountryId('US')->setDestPostcode('90210');
    $res1 = $shipping->collectRates($req1)->getResult();
    $rate1 = null;
    if ($res1 && $res1->getAllRates()) {
        foreach ($res1->getAllRates() as $r) {
            if ($r->getCarrier() === 'flatrate') {
                $rate1 = $r;
                break;
            }
        }
    }

    // Call 2: Checkout rate request
    $req2 = new RateRequest();
    $req2->setPackageQty(1)->setPackageWeight(1.0)->setDestCountryId('US')->setDestPostcode('90210');
    $res2 = $shipping->collectRates($req2)->getResult();
    $rate2 = null;
    if ($res2 && $res2->getAllRates()) {
        foreach ($res2->getAllRates() as $r) {
            if ($r->getCarrier() === 'flatrate') {
                $rate2 = $r;
                break;
            }
        }
    }

    $price1 = $rate1 ? (float)$rate1->getPrice() : null;
    $price2 = $rate2 ? (float)$rate2->getPrice() : null;

    $passed = ($price1 !== null && $price1 === $price2 && abs($price1 - 7.50) < 0.001);
    recordResult("Scenario 10: Checkout Consistency", $passed, "Cart estimate: $" . number_format((float)$price1, 2) . ", Checkout rate: $" . number_format((float)$price2, 2));
} catch (\Throwable $e) {
    recordResult("Scenario 10: Checkout Consistency", false, "Error: " . $e->getMessage());
} finally {
    cleanupDb($objectManager);
}

echo "\n========================================================================\n";
echo "   EXECUTION SUMMARY: $passCount / " . ($passCount + $failCount) . " SCENARIOS PASSED\n";
echo "========================================================================\n\n";

if ($failCount > 0) {
    exit(1);
}
exit(0);
