# Performance and Scalability Benchmark Report

## Extension Identity
- **Extension Name:** PriyankJivani_ShippingRules
- **Composer Package:** `priyankjivani/module-shipping-rules`
- **Environment:** Magento 2.4.9 Open Source, PHP 8.5.4, OPcache, MariaDB 11.8.6, Redis 8.0.5, OpenSearch 2.18

---

## 1. Executive Summary

The `PriyankJivani_ShippingRules` rate evaluation engine was subjected to high-throughput stress benchmarking across varying rule matrix sizes (10 to 500 rules) and cart item complexities (1 to 100 items).

### Highlights:
- **Sub-Millisecond Typical Latency:** In standard production environments (10-50 active shipping rules and 1-10 cart items), total rule processing time ranges between **0.134 ms and 1.229 ms**.
- **Extreme Scale Resilience:** Under an extreme load of **500 sequential shipping rules** and **100 cart items**, the complete calculation cycle completes in **92.285 ms** (averaging **0.18 ms per rule**).
- **Flat Memory Profile:** Zero memory leaks detected; memory overhead remained consistent at **~53.34 MB** throughout all stress iterations.

---

## 2. Benchmark Results Matrix

| Rules Count | Cart Items | Total Execution Time | Average per Rule | Peak Memory Usage | Evaluation Status |
|:-----------:|:----------:|:--------------------:|:----------------:|:-----------------:|:-----------------:|
| 10 | 1 | 0.134 ms | 0.0134 ms | 53.34 MB | PASS (Optimal) |
| 10 | 10 | 0.272 ms | 0.0272 ms | 53.34 MB | PASS (Optimal) |
| 10 | 50 | 1.008 ms | 0.1008 ms | 53.34 MB | PASS (Optimal) |
| 10 | 100 | 1.914 ms | 0.1914 ms | 53.34 MB | PASS (Optimal) |
| 50 | 1 | 0.372 ms | 0.0074 ms | 53.34 MB | PASS (Optimal) |
| 50 | 10 | 1.229 ms | 0.0246 ms | 53.34 ms | PASS (Optimal) |
| 50 | 50 | 4.861 ms | 0.0972 ms | 53.34 MB | PASS (Optimal) |
| 50 | 100 | 9.321 ms | 0.1864 ms | 53.34 MB | PASS (Optimal) |
| 100 | 1 | 0.717 ms | 0.0072 ms | 53.34 MB | PASS (Optimal) |
| 100 | 10 | 2.468 ms | 0.0247 ms | 53.34 MB | PASS (Optimal) |
| 100 | 50 | 9.608 ms | 0.0961 ms | 53.34 MB | PASS (Optimal) |
| 100 | 100 | 18.613 ms | 0.1861 ms | 53.34 MB | PASS (Optimal) |
| 500 | 1 | 3.512 ms | 0.0070 ms | 53.34 MB | PASS (High Throughput) |
| 500 | 10 | 11.669 ms | 0.0233 ms | 53.34 MB | PASS (High Throughput) |
| 500 | 50 | 48.846 ms | 0.0977 ms | 53.34 MB | PASS (High Throughput) |
| 500 | 100 | 92.285 ms | 0.1846 ms | 53.34 MB | PASS (High Throughput) |

---

## 3. Architecture & Optimization Pillars

1. **Selective Condition Tree Evaluation:** Rules with simple criteria (store ID, customer group, date/time boundaries, carriers) are evaluated before product condition subselection runs, terminating non-matching rules early.
2. **Pre-aggregated Cart Metrics:** Item weights, quantities, and subtotal metrics are computed efficiently once per calculation run.
3. **Priority Pipeline with Short-Circuiting (`discard_subsequent_rules`):** When exclusive or high-priority shipping rates are matched, subsequent rules are immediately bypassed to prevent redundant iterations.
4. **Zero-Allocation Postcode Pattern Engine:** Pre-compiled regular expressions and numerical range checks allow instant postcode lookups without expensive database joins.
