# Changelog

All notable changes to `PriyankJivani_ShippingRules` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [1.0.0] - 2026-11-09

### Added
- **Clean-room Shipping Rules Engine:** Independent Magento 2 architecture without third-party proprietary dependencies.
- **Admin UI Components:** Grids and edit forms for Manage Rules and Shipping Areas with mass actions (enable, disable, delete), export to CSV, and quick duplicate functionality.
- **4 Calculation Modes:** Replace, Surcharge, Discount (non-negative floor), and Partial Replace (mixed-cart calculation).
- **Dynamic Formula Calculator:** Base rate, per-product rate, per-unit weight rate, percentage of subtotal, and handling fee percentage.
- **Geographic Shipping Areas:** Reusable area definitions supporting countries, states/provinces, cities, numeric postcode ranges (`99501-99524`), wildcards (`995*`), and PO Box address line filters.
- **Nested Condition Tree:** Support for cart subtotal, weight, item quantity, shipping address, customer lifetime orders, customer lifetime spend, and product subselections.
- **Schedule Engine:** Date ranges, day of week multi-select, and time windows with store timezone evaluation and midnight crossing support.
- **Coupon & Backorder Controls:** Granular rules restricted to orders with specific promo codes or containing backordered inventory.
- **CSV Import / Export:** Bulk export and import mechanisms for both shipping rules and shipping areas with row-level validation.
- **Automated Testing Suite:** 17 isolated unit tests in PHPUnit and 10 end-to-end integration scenarios executed on live Magento 2.4.9.
- **Performance Benchmarking:** Stress-test benchmark measuring execution time and peak memory across 10 to 500 rules.
- **Comprehensive Documentation:** 13 technical manuals covering installation, configuration, user operations, testing, compatibility, security, and troubleshooting.
