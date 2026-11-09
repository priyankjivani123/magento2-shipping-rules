# Contributing Guidelines

Thank you for your interest in contributing to the `PriyankJivani_ShippingRules` module.

---

## 1. Development Principles

1. **Clean-Room Integrity:** All code must be original. Do not copy proprietary vendor code, assets, or trademarks.
2. **Magento Coding Standards:** Adhere strictly to PSR-12 and Magento 2 Coding Standards (MEQP).
3. **Decoupled Architecture:** Keep business logic separated into dedicated domain services (`RateCalculator`, `RuleEvaluator`, `ShippingAreaMatcher`, `RuleProcessor`).
4. **Test Coverage:** All bug fixes or new features must include PHPUnit unit tests and pass existing live integration suites.

---

## 2. Pull Request Workflow

1. Fork the repository.
2. Create a feature branch:
   ```bash
   git checkout -b feature/my-new-feature
   ```
3. Commit your changes following conventional commits:
   ```bash
   git commit -m "feat(calculator): add tier-based volume handling discount"
   ```
4. Run the test suite:
   ```bash
   ./vendor/bin/phpunit app/code/PriyankJivani/ShippingRules/Test/Unit
   ```
5. Push to your branch and open a Pull Request.
