# Compatibility & Requirements Matrix

This extension has been built using clean-room standards and verified against modern Magento 2 infrastructure.

---

## 1. Supported Magento Versions

| Magento Edition | Supported Versions | Status |
|:----------------|:-------------------|:-------|
| **Magento Open Source** | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8, 2.4.9 | Fully Verified |
| **Adobe Commerce (On-Premises)** | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8, 2.4.9 | Supported |
| **Adobe Commerce on Cloud** | 2.4.4+ | Supported |

---

## 2. PHP Version Compatibility

| PHP Version | Compatibility | Notes |
|:------------|:-------------:|:------|
| **PHP 8.2** | Compatible | Minimum required for Magento 2.4.6+ |
| **PHP 8.3** | Compatible | Recommended for Magento 2.4.7+ |
| **PHP 8.4** | Compatible | Verified with explicit nullable parameter types |
| **PHP 8.5** | Compatible | Live verified on active testbed (PHP 8.5.4) |

---

## 3. Database & Search Engines

| Technology | Supported Versions |
|:-----------|:-------------------|
| **MariaDB** | 10.6, 10.11, 11.4, 11.8+ |
| **MySQL** | 8.0, 8.4 LTS |
| **OpenSearch** | 1.3+, 2.5+, 2.18+ |
| **Elasticsearch** | 7.17+, 8.x |
| **Redis** | 6.2+, 7.0+, 8.0+ |

---

## 4. Frontend & Checkout Compatibility

| Frontend / Theme | Compatibility | Notes |
|:-----------------|:-------------:|:------|
| **Magento Luma / Blank** | Fully Compatible | Default standard theme |
| **Hyvä Themes** | Fully Compatible | Intercepts at rate collection backend level |
| **Default One-Step Checkout** | Fully Compatible | Real-time rate updates on address change |
| **Headless / PWA Studio / GraphQL** | Fully Compatible | Native GraphQL quote shipping queries supported |
