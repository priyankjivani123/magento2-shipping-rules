# Installation Guide

This document outlines the standard installation methods for the `PriyankJivani_ShippingRules` module on Magento Open Source and Adobe Commerce 2.4.x.

---

## Prerequisites

- **Magento:** 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8, 2.4.9
- **PHP:** 8.2, 8.3, 8.4, 8.5
- **Database:** MariaDB 10.6+ / 11.x or MySQL 8.0+
- **Composer:** v2.2+

---

## Method 1: Composer Installation (Recommended)

1. Require the package via Composer:
   ```bash
   composer require priyankjivani/module-shipping-rules
   ```

2. Enable the module:
   ```bash
   php bin/magento module:enable PriyankJivani_ShippingRules
   ```

3. Run database migrations and upgrade schema:
   ```bash
   php bin/magento setup:upgrade
   ```

4. Compile dependency injection and code generators:
   ```bash
   php bin/magento setup:di:compile
   ```

5. Deploy static content (Production mode only):
   ```bash
   php bin/magento setup:static-content:deploy -f
   ```

6. Flush Magento cache:
   ```bash
   php bin/magento cache:clean
   ```

---

## Method 2: Manual Installation (app/code)

1. Clone or unpack the repository into:
   ```
   app/code/PriyankJivani/ShippingRules
   ```

2. Verify directory layout:
   ```
   app/code/PriyankJivani/ShippingRules/
   ├── Api/
   ├── Block/
   ├── Controller/
   ├── Model/
   ├── Plugin/
   ├── Setup/
   ├── Ui/
   ├── view/
   ├── composer.json
   └── registration.php
   ```

3. Enable the extension:
   ```bash
   php bin/magento module:enable PriyankJivani_ShippingRules
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento cache:clean
   ```

---

## Verification

To verify that the module is installed and active:

```bash
php bin/magento module:status PriyankJivani_ShippingRules
```

Expected output:
```
Module is enabled
```

Check database tables:
```bash
mysql -u <user> -p <dbname> -e "SHOW TABLES LIKE 'priyankjivani_%';"
```
Output:
- `priyankjivani_shipping_rule`
- `priyankjivani_shipping_rule_store`
- `priyankjivani_shipping_rule_customer_group`
- `priyankjivani_shipping_area`

---

## Uninstalling the Module

```bash
php bin/magento module:disable PriyankJivani_ShippingRules
php bin/magento cache:clean
```
