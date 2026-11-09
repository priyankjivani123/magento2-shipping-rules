# Security Policy & Safeguards

The `PriyankJivani_ShippingRules` extension is engineered with defense-in-depth principles to protect store stability, checkout integrity, and administrative boundaries.

---

## 1. Access Control & ACL

All administrative features are protected by granular Magento 2 Access Control Lists (`etc/acl.xml`):
- `PriyankJivani_ShippingRules::shipping_rules` - Top-level menu access.
- `PriyankJivani_ShippingRules::manage_rules` - View, create, edit, duplicate, and delete shipping rules.
- `PriyankJivani_ShippingRules::shipping_areas` - View, create, edit, duplicate, and delete shipping areas.
- `PriyankJivani_ShippingRules::config` - Modify global system configuration settings.

Unauthorized administrative roles are blocked at the HTTP controller level via `public const ADMIN_RESOURCE`.

---

## 2. Safe Regular Expression Engine (ReDoS Protection)

Administrator-entered regular expressions on shipping addresses and postcodes are subjected to:
1. **Delimiter Injection Prevention:** Patterns lacking delimiters are safely enclosed with `/.../i`.
2. **Execution Error Handling:** All regex matching is wrapped in exception handlers (`try / @preg_match`), preventing malformed expressions from crashing PHP execution.
3. **No Execution of User-Supplied Code:** Conditions only parse pre-defined operators; dynamic evaluation (`eval`, string function execution) is strictly prohibited.

---

## 3. Financial Invariant Safeguards

1. **Strict Non-Negative Pricing Guarantee:**
   Shipping rates can never become negative under any circumstance. When discounts exceed the original shipping amount, the final rate is capped at `$0.00`.
2. **Float Precision Clamping:**
   Calculations are rounded to standard 2-decimal currency precision to avoid floating-point rounding artifacts.

---

## 4. Reporting Security Vulnerabilities

If you discover a security vulnerability in this module, please report it privately to:
- **Email:** `priyank.jivani@example.com`
- Do not disclose security issues publicly in public issues or social media. We will respond within 48 hours with an assessment and remediation plan.
