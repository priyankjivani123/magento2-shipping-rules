# Troubleshooting Guide

Solutions and diagnostics for common questions and configuration scenarios.

---

## 1. Shipping Rule Is Not Applying

### Symptom:
A configured shipping rule does not seem to change shipping prices in the storefront cart or checkout.

### Diagnostic Checklist:
1. **Rule Status:** Verify that **Enable Rule** is toggled to `Yes`.
2. **Global Setting:** Ensure **Stores → Configuration → Sales → Shipping Rules → Enable Shipping Rules** is set to `Yes`.
3. **Store Views:** Check if the rule is assigned to the current Store View or *All Store Views*.
4. **Customer Group:** If testing as a guest, ensure *NOT LOGGED IN* is included in customer groups.
5. **Carrier Match:** If carriers are selected, verify that the carrier code matches the active shipping method (e.g., `flatrate`).
6. **Date & Time Boundaries:** Verify that your server and store timezone match the current time window configured on the rule.
7. **Priority Conflict:** Check if a higher-priority rule has **Discard Subsequent Rules** enabled and is halting execution early.

### Action:
Enable **Debug Logging** in **Stores → Configuration → Sales → Shipping Rules**, reproduce the rate calculation in cart, and inspect:
```bash
tail -n 50 /var/www/magento2/var/log/shipping_rules.log
```

---

## 2. Shipping Rate Shows $0.00 Unexpectedly

### Symptom:
A discount rule reduces the shipping price to $0.00.

### Cause:
The discount formula produced an amount exceeding the original shipping price. By default, the extension prevents negative rates by capping the discount at the original price ($0.00 floor).

### Solution:
Set a **Minimum Total Rate ($)** on the rule (e.g., `5.00`) to guarantee a baseline charge.

---

## 3. Postcode Range Not Matching

### Symptom:
A postcode range like `99501-99524` fails to match.

### Cause:
Ensure there are no accidental spaces inside the hyphen (use `99501-99524`, not `99501 - 99524`). For UK or alphanumeric postcodes (e.g., `SW1A 1AA`), use wildcard prefix patterns like `SW1A*` instead of numeric ranges.

---

## 4. UI Grid Shows "No Records Found" After Import

### Solution:
Flush the configuration and collections cache:
```bash
php bin/magento cache:clean
```
