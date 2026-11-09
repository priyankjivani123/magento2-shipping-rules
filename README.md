# Magento 2 Advanced Shipping Rules Extension

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Magento 2.4.x](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.9-orange.svg)](https://magento.com)
[![PHP 8.2 - 8.5](https://img.shields.io/badge/PHP-8.2%20--%208.5-purple.svg)](https://php.net)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![Tests](https://img.shields.io/badge/tests-17%20passed%20(100%25)-success.svg)]()
[![Integration](https://img.shields.io/badge/e2e%20integration-10%20passed%20(100%25)-success.svg)]()

A high-performance, clean-room Magento 2 shipping management extension designed to dynamically adjust, surcharge, discount, or completely replace carrier shipping rates based on flexible conditions including cart subtotal, item weight, product attributes, shipping areas, customer groups, schedules, and coupon codes.

---

## Key Features

- **4 Calculation Modes:**
  - **Replace:** Overwrite carrier shipping rates with a fixed fee, per-item rate, or weight fee.
  - **Surcharge:** Add freight handling fees, remote zone charges, or fuel adjustments on top of original carrier rates.
  - **Discount:** Deduct promotional discounts or percentage concessions from shipping charges (guaranteed never negative).
  - **Partial Replace (Mixed Cart):** Dynamically split rates between matching and non-matching products in mixed baskets.
- **Dynamic Formula Calculator:**
  - Base flat fee (`$`)
  - Per product / quantity fee (`$`)
  - Per unit weight fee (`$/lb` or `$/kg`)
  - Percentage of product subtotal (`%`)
  - Handling percentage applied to carrier rate (`%`)
  - Clamping boundaries: Min/Max rate change and Min/Max total shipping rate floor/cap.
- **Geographic Shipping Areas:**
  - Group destination countries, states/provinces, cities, and postcode patterns.
  - Multi-syntax postcode matching: exact codes (`90210`), numeric ranges (`99501-99695`), and wildcards (`995*`).
  - PO Box and military deployment address filter (`APO`, `FPO`, `DPO`, `Suite`).
- **Comprehensive Condition Engine:**
  - Cart attributes (subtotal, weight, quantity, coupon code).
  - Shipping address attributes (country, state, city, postcode regex, shipping area).
  - Customer profile attributes (logged in / guest, customer group code, orders count, total spent).
  - Product subselections (category IDs, SKU patterns, attributes, pricing thresholds).
  - Safe regex engine that guarantees store stability even with invalid user patterns.
- **Timezone-Aware Scheduling & Day Restrictions:**
  - Restrict rules by start date, expiration date, active days of the week, and active time windows (supports crossing midnight).
  - Evaluated against store-configured timezone.
- **Promotional Coupons & Backorders:**
  - Require specific promo coupon codes or exclude promo orders.
  - Restrict rules to carts containing backordered items or in-stock only items.
- **Priority Pipeline & Short-Circuiting:**
  - Sort rule execution by numerical priority (0 is highest).
  - "Discard Subsequent Rules" option halts lower priority rules once a match occurs.
- **Enterprise Performance:**
  - Sub-millisecond evaluation latency (0.13 ms for 10 rules; 92 ms for 500 rules across 100 cart items).
  - Fully compatible with Redis cache, OpenSearch 2.x, and PHP 8.2 through 8.5.

---

## Live Demo Endpoints

- **Storefront:** [http://152.67.31.72/](http://152.67.31.72/)
- **Admin Panel:** [http://152.67.31.72/admin/](http://152.67.31.72/admin/)
  - **Username:** `admin`
  - **Password:** `PriyankAdmin123!`
  - **Navigation:** `Sales` → `Shipping Rules` → `Manage Rules` and `Stores` → `Shipping Areas`

---

## Documentation Index

1. [Installation Guide](INSTALLATION.md) - Composer and manual zip installation steps.
2. [Configuration Reference](CONFIGURATION.md) - Global settings and diagnostics.
3. [User Guide](USER_GUIDE.md) - Complete walkthrough for store administrators.
4. [Conditions Reference](CONDITIONS_REFERENCE.md) - Complete schema of all supported cart, customer, and product conditions.
5. [Rate Calculation Guide](RATE_CALCULATION.md) - In-depth formulas for all 4 calculation types.
6. [Shipping Areas Guide](SHIPPING_AREAS.md) - Creating geographic clusters, postcode ranges, and address line rules.
7. [Testing & Verification](TESTING.md) - PHPUnit and live E2E integration test suites.
8. [Performance & Benchmarks](PERFORMANCE-REPORT.md) - Stress-test metrics and memory consumption.
9. [Compatibility Guide](COMPATIBILITY.md) - Supported Magento, PHP, database, and checkout stacks.
10. [Troubleshooting Guide](TROUBLESHOOTING.md) - Solutions for common configuration issues.
11. [Security Guide](SECURITY.md) - Access control, safe regex parsing, and input sanitation.
12. [Changelog](CHANGELOG.md) - Release notes and version history.
13. [Contributing Guidelines](CONTRIBUTING.md) - Standards for contributing to this project.

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
