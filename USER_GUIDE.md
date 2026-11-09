# Administrator User Guide

A step-by-step walkthrough for configuring dynamic shipping rules and geographic areas.

---

## 1. Creating a New Shipping Rule

1. Navigate to **Sales → Shipping Rules → Manage Rules**.
2. Click **Add New Rule** in the top-right corner.
3. Configure the rule tabs:

### Tab 1: General Information
- **Enable Rule:** Toggle to activate or pause the rule.
- **Rule Name:** Descriptive internal title (e.g., `Alaska $35 Air Surcharge`).
- **Description:** Internal notes on business justification or campaign details.
- **Priority:** Execution order (e.g., `10`). Lower values run first (`0` is highest priority).
- **Store Views:** Restrict to specific store views or select *All Store Views*.
- **Customer Groups:** Restrict to *NOT LOGGED IN*, *General*, *Wholesale*, or *Retailer*.
- **Discard Subsequent Rules:** When enabled, stops processing lower-priority shipping rules once this rule matches.

### Tab 2: Shipping Carriers & Methods
- **Apply to Carriers:** Select target carriers (e.g., `Flat Rate`, `UPS`, `FedEx`, `USPS`). Leave unselected to apply to all carriers.
- **Apply to Specific Methods:** Select granular carrier methods (e.g., `Flat Rate - Fixed`, `UPS - Ground`, `FedEx - Express Saver`).

### Tab 3: Rate Calculation & Adjustments
- **Calculation Type:** Choose one of four calculation methods:
  - `Replace` - Completely overwrite the carrier shipping price.
  - `Surcharge` - Add an additional fee to the carrier shipping price.
  - `Discount` - Deduct an amount from the shipping rate (never drops below $0.00).
  - `Partial Replace` - Mixed basket logic (replaces rate only for matching products).
- **Base Rate ($):** Fixed rate adjustment.
- **Rate Per Product ($):** Multiplied by the quantity of items in the cart.
- **Rate Per Unit Weight ($):** Multiplied by the total package weight (lbs or kg).
- **Percentage of Product Price (%):** Surcharge or discount based on order item subtotal.
- **Handling Fee (%):** Percentage applied directly to original carrier price.
- **Min / Max Rate Change ($):** Clamps the maximum adjustment amount.
- **Min / Max Total Rate ($):** Sets a hard floor and ceiling for the final shipping price.
- **Custom Carrier / Method Title:** Overrides the carrier or method name displayed to the customer at checkout.

### Tab 4: Conditions
- Configure condition trees combining cart attributes (subtotal, weight, quantity), destination address attributes (country, state, postcode, shipping area), customer attributes, and product subselections.

### Tab 5: Schedule, Coupons & Backorders
- **From / To Date:** Date range when the rule is active.
- **Days of Week:** Limit rule to specific days (e.g., weekends only).
- **Active Time Window:** Restrict to hours of operation (e.g., `09:00:00` to `17:00:00`), with full support for crossing midnight (e.g., `22:00:00` to `06:00:00`).
- **Coupon Mode:** Apply with any coupon, only when a specific promo coupon is applied, or only when no coupon is used.
- **Backorder Restriction:** Apply to all orders, orders containing backordered items only, or in-stock only orders.

4. Click **Save Rule**.

---

## 2. Managing Shipping Areas

Navigate to **Stores → Shipping Areas**.

1. Click **Add New Shipping Area**.
2. Fill in:
   - **Area Name:** E.g., `European Union Zone` or `Remote US Territories`.
   - **Countries:** Multi-select destination countries.
   - **Regions:** Multi-select or enter region/state codes.
   - **Cities:** Enter one city per line.
   - **Postcodes:** Enter postal codes. Supports:
     - Exact match: `90210`
     - Ranges: `99501-99695`
     - Wildcards: `995*`
   - **Address Lines:** Filter PO Box, APO, FPO, or Suite address lines.
   - **Address Lines Mode:** `Include` (applies only if address matches pattern) or `Exclude` (disqualifies address if pattern matches).
3. Click **Save Shipping Area**.

---

## 3. Duplicating Rules & Shipping Areas

To quickly create variations of an existing rule:
1. In the rule or area listing grid, click the **Action** column dropdown.
2. Select **Duplicate**.
3. A clone is created in `Disabled` status with `(Duplicate)` appended to the name, ready for modification.

---

## 4. Bulk Mass Actions

From the grid toolbar:
- Select checkboxes next to multiple rules.
- Open the **Actions** dropdown:
  - **Enable:** Activate selected rules in bulk.
  - **Disable:** Pause selected rules in bulk.
  - **Delete:** Permanently remove selected rules with confirmation.
- Open the **Export** button to download all or filtered records as a CSV spreadsheet.
