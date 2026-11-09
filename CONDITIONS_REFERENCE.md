# Rule Conditions Reference Manual

The `PriyankJivani_ShippingRules` module features a robust condition tree supporting nested combinations (`ALL`, `ANY`, `TRUE`, `FALSE`) across cart attributes, customer attributes, shipping address attributes, and product subselections.

---

## 1. Cart & Address Attributes

| Attribute Code | Display Label | Supported Operators | Value Examples | Description |
|:--------------|:--------------|:--------------------|:---------------|:------------|
| `base_subtotal` | Subtotal (Base Currency) | `==`, `!=`, `>=`, `>`, `<=`, `<` | `75.00` | Cart subtotal before tax and shipping in base currency. |
| `weight` | Total Package Weight | `==`, `!=`, `>=`, `>`, `<=`, `<` | `10.5` | Sum of item weights in the cart. |
| `total_qty` | Total Cart Items Quantity | `==`, `!=`, `>=`, `>`, `<=`, `<` | `5` | Total count of units across all products in cart. |
| `country_id` | Destination Country | `==`, `!=`, `()`, `!()` | `US`, `CA,MX` | ISO-2 country code of the shipping address. |
| `region_id` | State / Province ID | `==`, `!=`, `()`, `!()` | `12` | Numeric region ID from Magento directory table. |
| `region` | State / Province Name | `==`, `!=`, `{}`, `!{}`, `()`, `!()` | `California`, `CA` | Text name or region code. |
| `city` | City | `==`, `!=`, `{}`, `!{}`, `()`, `!()` | `New York`, `Anchorage` | Destination city (case-insensitive). |
| `postcode` | ZIP / Postal Code | `==`, `!=`, `{}`, `!{}`, `()`, `!()`, `matches` | `90210`, `995*`, `/^99[5-9]/` | Supports exact match, prefix wildcards, and safe regular expressions. |
| `street` | Street Address | `{}`, `!{}`, `matches` | `PO Box`, `Suite` | Searches address lines 1-4 for specified keywords. |
| `shipping_area` | Shipping Area | `==`, `!=` | Area dropdown | Matches pre-configured reusable Shipping Areas. |
| `coupon_code` | Applied Coupon Code | `==`, `!=`, `{}`, `!{}` | `FREESHIP` | Active coupon code applied to the quote. |

---

## 2. Customer Attributes

| Attribute Code | Display Label | Supported Operators | Value Examples | Description |
|:--------------|:--------------|:--------------------|:---------------|:------------|
| `customer_is_guest` | Customer Is Guest | `==`, `!=` | `1` (Yes) / `0` (No) | Differentiates guest shoppers from registered users. |
| `customer_group_id` | Customer Group Code | `==`, `!=`, `()`, `!()` | `Wholesale`, `General` | Customer group assignment. |
| `orders_count` | Customer Lifetime Orders | `==`, `!=`, `>=`, `>`, `<=`, `<` | `5` | Lifetime completed order count for registered customer. |
| `total_spent` | Customer Lifetime Spent ($) | `==`, `!=`, `>=`, `>`, `<=`, `<` | `1000.00` | Lifetime aggregate spend across completed orders. |

---

## 3. Product Subselection Conditions

Admins can evaluate conditions against specific items within the cart:
- **Category:** E.g., `Category IS ONE OF [24, 25]` (Heavy Freight / Hazardous materials).
- **SKU:** E.g., `SKU IS NOT [LUMA-BAG01]`.
- **Attribute Set:** E.g., `Attribute Set IS Apparel`.
- **Item Weight:** E.g., `Item Weight >= 20.0 lbs`.
- **Item Price:** E.g., `Price >= $100.00`.

---

## 4. Safe Regular Expression Evaluation

When using the `matches` regex operator on postcodes or streets:
1. Patterns are automatically delimited if delimiters are omitted:
   `^995[0-9]{2}$` becomes `/^995[0-9]{2}$/i`.
2. Syntax errors, catastrophic backtracking patterns, and unclosed parentheses are caught safely with error suppression (`@preg_match`).
3. Invalid regex entries return `false` cleanly without throwing uncaught PHP exceptions or disrupting checkout.
