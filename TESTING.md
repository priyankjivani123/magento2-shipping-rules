# Testing & Verification Guide

This document describes the automated test architecture, coverage, and instructions for running the test suites.

---

## 1. Automated Test Suites

The extension features two testing layers:
1. **Unit Test Suite (PHPUnit):** Fast, isolated tests verifying calculation algorithms, schedule validation, timezone handling, and postcode regex matching.
2. **Live E2E Integration Suite:** End-to-end integration tests executing against the live Magento 2.4.9 object manager, MariaDB database, quote repositories, and shipping rate collectors.

---

## 2. Running Unit Tests

Run all unit tests via PHPUnit from the Magento root directory:

```bash
cd /var/www/magento2
./vendor/bin/phpunit app/code/PriyankJivani/ShippingRules/Test/Unit
```

### Test Classes:
- `Test/Unit/Model/Service/RateCalculatorTest.php` - Verifies Replace, Surcharge, Discount, Partial Replace, min/max clamps, floor limits, and rounding.
- `Test/Unit/Model/Service/ShippingAreaMatcherTest.php` - Verifies country inclusion/exclusion, numeric postcode ranges, wildcards, and PO Box filters.
- `Test/Unit/Model/Service/RuleEvaluatorTest.php` - Verifies active dates, day-of-week restrictions, store views, and midnight-crossing time windows.

Expected Output:
```
PHPUnit 12.5.14 by Sebastian Bergmann and contributors.
Runtime:       PHP 8.5.4
OK (17 tests, 37 assertions)
```

---

## 3. Running Live E2E Integration Tests

Execute the 10 real-world scenarios on the live environment:

```bash
php /var/www/magento2/app/code/PriyankJivani/ShippingRules/Test/Integration/test_all_scenarios.php
```

### 10 Live Test Scenarios:
1. **Scenario 1:** Alaska Surcharge ($10 base + $50 Alaska surcharge = $60.00)
2. **Scenario 2:** Weight-based Surcharge (5kg * $1.00 = $5.00)
3. **Scenario 3:** Customer Group Discount (Wholesale 10% off $20.00 = $18.00)
4. **Scenario 4:** Mixed Cart Partial Replacement
5. **Scenario 5:** Coupon Restriction (Only applies with promo code `SAVE10`)
6. **Scenario 6:** Priority & Discard Subsequent Rules (Stops pipeline at priority 1)
7. **Scenario 7:** Shipping Area Exclusions (Hawaii matches; New York excluded)
8. **Scenario 8:** Schedule & Day Boundaries (Weekday vs weekend boundary checking)
9. **Scenario 9:** Backorders Mode (Cart with backordered items vs in-stock items)
10. **Scenario 10:** Checkout Consistency (Cart estimate matches checkout rate matches order rate)

Expected Output:
```
========================================================================
   PRIYANK JIVANI - MAGENTO 2 SHIPPING RULES E2E INTEGRATION SUITE       
========================================================================

[PASS] Scenario 1: Alaska Surcharge - Expected: $60.00, Actual: $60.00
[PASS] Scenario 2: Weight-based Surcharge - Expected: $15.00, Actual: $15.00
[PASS] Scenario 3: Customer Group Discount - Expected: $18.00, Actual: $18.00
[PASS] Scenario 4: Mixed Cart Partial Replacement - Actual: $5.00
[PASS] Scenario 5: Coupon Restriction - With SAVE10: $0, Without: $15
[PASS] Scenario 6: Priority & Discard Subsequent - Expected: $12.00, Actual: $12.00
[PASS] Scenario 7: Shipping Area Exclusions - HI matched: Yes, NY matched: No
[PASS] Scenario 8: Schedule & Day Boundaries - Current day (1) evaluated correctly as INACTIVE
[PASS] Scenario 9: Backorders Mode - In-stock applied: No, Backorder applied: Yes
[PASS] Scenario 10: Checkout Consistency - Cart estimate: $7.50, Checkout rate: $7.50

========================================================================
   EXECUTION SUMMARY: 10 / 10 SCENARIOS PASSED
========================================================================
```

---

## 4. Running Performance Benchmarks

Run the high-load benchmark suite (10 to 500 rules across 1 to 100 cart items):

```bash
php /var/www/magento2/app/code/PriyankJivani/ShippingRules/Test/Performance/benchmark.php
```
