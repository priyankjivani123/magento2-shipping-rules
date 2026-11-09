<?php
declare(strict_types=1);

/**
 * Performance & Scalability Benchmark Suite
 * Tests rule evaluation throughput and memory overhead with varying rule counts and cart sizes.
 */

use Magento\Framework\App\Bootstrap;
use PriyankJivani\ShippingRules\Api\Data\RuleInterface;
use PriyankJivani\ShippingRules\Model\Service\RuleProcessor;
use PriyankJivani\ShippingRules\Model\Service\RateCalculator;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;

require '/var/www/magento2/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$rateResultMethodFactory = $objectManager->get(MethodFactory::class);
$rateCalculator = $objectManager->get(RateCalculator::class);

echo "\n========================================================================\n";
echo "   PRIYANK JIVANI - SHIPPING RULES PERFORMANCE BENCHMARK SUITE          \n";
echo "========================================================================\n\n";

$ruleCounts = [10, 50, 100, 500];
$cartSizes = [1, 10, 50, 100];

// Prepare mock rule models
function generateMockRules(int $count, $objectManager): array {
    $rules = [];
    $ruleFactory = $objectManager->get(\PriyankJivani\ShippingRules\Model\RuleFactory::class);

    for ($i = 1; $i <= $count; $i++) {
        $rule = $ruleFactory->create();
        $rule->setRuleId($i);
        $rule->setName("Benchmark Rule #$i");
        $rule->setIsActive(true);
        $rule->setPriority($i);
        $rule->setCalculationType(RuleInterface::CALC_SURCHARGE);
        $rule->setCalcBaseRate(1.50);
        $rule->setCalcPerProduct(0.25);
        $rule->setCalcPerWeight(0.10);
        $rules[] = $rule;
    }
    return $rules;
}

// Prepare mock cart items
function generateMockCart(int $itemCount): RateRequest {
    $request = new RateRequest();
    $items = [];
    $totalQty = 0;
    $totalWeight = 0.0;

    for ($i = 1; $i <= $itemCount; $i++) {
        $item = new \Magento\Framework\DataObject([
            'parent_item' => null,
            'qty' => 1,
            'weight' => 2.5,
            'base_row_total' => 25.00
        ]);
        $items[] = $item;
        $totalQty += 1;
        $totalWeight += 2.5;
    }

    $request->setAllItems($items);
    $request->setPackageQty($totalQty);
    $request->setPackageWeight($totalWeight);
    return $request;
}

$results = [];

printf("%-12s | %-12s | %-15s | %-15s | %-12s\n", "Rules Count", "Cart Items", "Execution Time", "Avg / Rule", "Peak Memory");
echo str_repeat("-", 76) . "\n";

foreach ($ruleCounts as $rCount) {
    $rules = generateMockRules($rCount, $objectManager);

    foreach ($cartSizes as $cSize) {
        $request = generateMockCart($cSize);
        $rate = $rateResultMethodFactory->create();
        $rate->setCarrier('flatrate')->setMethod('flatrate')->setPrice(10.00);

        gc_collect_cycles();
        $startMem = memory_get_usage();
        $startTime = microtime(true);

        // Execute rate calculations
        $currentPrice = 10.00;
        foreach ($rules as $rule) {
            $calc = $rateCalculator->calculate($rule, $currentPrice, $request);
            $currentPrice = (float)$calc['final_price'];
        }

        $endTime = microtime(true);
        $peakMem = memory_get_peak_usage();

        $elapsedMs = ($endTime - $startTime) * 1000.0;
        $avgPerRuleMs = $elapsedMs / $rCount;
        $memMb = $peakMem / (1024 * 1024);

        $results[] = [
            'rules' => $rCount,
            'cart_items' => $cSize,
            'elapsed_ms' => round($elapsedMs, 3),
            'avg_per_rule_ms' => round($avgPerRuleMs, 4),
            'peak_memory_mb' => round($memMb, 2)
        ];

        printf("%-12d | %-12d | %-12.3f ms | %-12.4f ms | %-9.2f MB\n",
            $rCount,
            $cSize,
            $elapsedMs,
            $avgPerRuleMs,
            $memMb
        );
    }
}

echo str_repeat("=", 76) . "\n";
echo "Benchmark completed successfully.\n";

// Save results to scratch JSON for documentation generation
file_put_contents('/var/www/magento2/app/code/PriyankJivani/ShippingRules/Test/Performance/benchmark_results.json', json_encode($results, JSON_PRETTY_PRINT));
