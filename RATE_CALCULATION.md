# Shipping Rate Calculation Guide

This document details the mathematical algorithms and evaluation pipelines utilized by `PriyankJivani_ShippingRules` to adjust shipping costs.

---

## 1. Raw Adjustment Formula

For any matched shipping rule, the initial raw adjustment amount (`A_raw`) is computed as:

$$A_{\text{raw}} = \text{Base} + (\text{Qty} \times \text{Rate}_{\text{product}}) + (\text{Weight} \times \text{Rate}_{\text{weight}}) + (\text{Subtotal} \times \text{Percent}_{\text{product}}) + (\text{Rate}_{\text{orig}} \times \text{Handling}_{\%})$$

Where:
- $\text{Base}$: Fixed base dollar amount (`calc_base_rate`).
- $\text{Qty}$: Total item count (or count of matching items in mixed cart).
- $\text{Rate}_{\text{product}}$: Cost per product item (`calc_per_product`).
- $\text{Weight}$: Total package weight in lbs or kg.
- $\text{Rate}_{\text{weight}}$: Cost per unit weight (`calc_per_weight`).
- $\text{Subtotal}$: Cart subtotal (or subtotal of matching items).
- $\text{Percent}_{\text{product}}$: Product price percentage fee (`calc_percent_product` / 100).
- $\text{Rate}_{\text{orig}}$: Original shipping rate quoted by carrier.
- $\text{Handling}_{\%}$: Handling percentage fee (`calc_handling_percent` / 100).

---

## 2. Rate Adjustment Clamping

If minimum or maximum adjustment bounds are configured:

$$\text{MinRateChange} \le A_{\text{clamped}} \le \text{MaxRateChange}$$

---

## 3. Four Calculation Modes

### Mode 1: Replace (`replace`)
The carrier rate is overwritten entirely by the calculated adjustment:
$$\text{Rate}_{\text{final}} = A_{\text{clamped}}$$

*Use Case:* Free shipping tier ($\text{Base} = 0$), flat rate override ($\text{Base} = 15.00$).

---

### Mode 2: Surcharge (`surcharge`)
The calculated adjustment is added onto the original carrier rate:
$$\text{Rate}_{\text{final}} = \text{Rate}_{\text{orig}} + A_{\text{clamped}}$$

*Use Case:* Alaska/Hawaii remote air freight fee (+$35.00), heavy package fee (+$2.00/lb).

---

### Mode 3: Discount (`discount`)
The calculated amount is deducted from the original carrier rate:
$$\text{Discount} = \min(\text{Rate}_{\text{orig}}, \max(0, A_{\text{clamped}}))$$
$$\text{Rate}_{\text{final}} = \max(0.00, \text{Rate}_{\text{orig}} - \text{Discount})$$

*Safety Guarantee:* Shipping rates are guaranteed to never be negative ($\ge \$0.00$).

*Use Case:* 15% VIP wholesale shipping discount, $5 promo shipping voucher.

---

### Mode 4: Partial Replace (`partial_replace`)
Designed for mixed shopping carts where only certain items match specific shipping rules (e.g., standard items combined with hazardous freight).

$$\text{Ratio}_{\text{non-matching}} = \frac{\text{Qty}_{\text{cart}} - \text{Qty}_{\text{matching}}}{\text{Qty}_{\text{cart}}}$$
$$\text{Rate}_{\text{final}} = (\text{Rate}_{\text{orig}} \times \text{Ratio}_{\text{non-matching}}) + A_{\text{clamped}}$$

*Use Case:* A cart contains 2 t-shirts and 1 surfboard. The t-shirts pay proportional standard shipping while the surfboard receives custom freight rate calculation.

---

## 4. Total Rate Clamping & Rounding

Finally, the rate is clamped between configured store floors and ceilings:

$$\text{MinTotalRate} \le \text{Rate}_{\text{final}} \le \text{MaxTotalRate}$$

All rates are rounded to 2 decimal places:
$$\text{Rate}_{\text{final}} = \text{round}(\text{Rate}_{\text{final}}, 2)$$
