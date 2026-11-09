# Global Configuration Reference

Navigate to **Stores → Configuration → Sales → Shipping Rules** in the Magento Admin panel.

---

## Configuration Settings

### 1. General Settings

| Field | Path | Options / Default | Description |
|:------|:-----|:-----------------:|:------------|
| **Enable Shipping Rules** | `sales/priyankjivani_shippingrules/enabled` | `Yes / No` (Default: `Yes`) | Globally activates or disables the shipping rule evaluation engine without uninstalling the module. |
| **Debug Logging** | `sales/priyankjivani_shippingrules/debug_logging` | `Yes / No` (Default: `No`) | When enabled, detailed execution logs (rule ID, matched conditions, price transformations, and execution duration) are written to `var/log/shipping_rules.log`. |

---

## Admin Menu Navigation

1. **Rule Management:**
   - Go to: **Sales → Shipping Rules → Manage Rules**
   - Access: View, search, filter, duplicate, enable, disable, delete, and export shipping rules to CSV.

2. **Shipping Areas:**
   - Go to: **Stores → Shipping Areas**
   - Access: Create and manage reusable geographic shipping zones with country, state, city, and postcode patterns.

---

## Diagnostics and Log Monitoring

When **Debug Logging** is enabled, view live calculation tracing:

```bash
tail -f /var/www/magento2/var/log/shipping_rules.log
```

Sample log entry:
```
[2026-11-09 12:15:30] main.INFO: [PriyankJivani_ShippingRules] Rule #1 ("Alaska & Hawaii Air Surcharge") applied to flatrate_flatrate. Price adjusted from 10.00 to 45.00
[2026-11-09 12:15:30] main.INFO: [PriyankJivani_ShippingRules] Rule #5 has Discard Subsequent Rules active. Halting execution for flatrate_flatrate.
```
