# Frontend Variant Implementation Guide

## Overview

Customers can now view parent products with variant selectors and choose different sizes/strengths before adding to cart.

## Product Detail Page (`medicines/show.blade.php`)

### What's New:

1. **Variant Selector**
    - Displayed only if the product is a parent with variants
    - Shows variant buttons (e.g., "10 ml", "20 ml", "50 ml")
    - Selected variant is highlighted with blue ring
    - Displays variant stock availability

2. **Dynamic Price Display**
    - Price updates based on selected variant
    - Shows discount percentage for each variant
    - "You save" amount updates accordingly
    - MRP reflects variant pricing

3. **Stock Indicator**
    - Shows selected variant's stock in real-time
    - Color-coded (red/amber/green) based on availability

4. **Add to Cart Integration**
    - Automatically uses selected variant ID
    - If parent product, shows error: "Please select a variant before adding"
    - Cart maintains separate entries for each variant

## User Flow:

### Viewing a Product with Variants:

1. Customer clicks on "Himalaya Face Wash" product
2. Product page loads showing:
    - Main product info (name, manufacturer, category)
    - **Variant selector** with buttons: 10 ml, 20 ml, 50 ml, 100 ml
    - First variant (10 ml) is auto-selected
    - Price/MRP/stock for selected variant displayed
3. Customer can click different size buttons to see:
    - Different pricing if applicable
    - Different stock levels
    - Updated "You save" amount
4. Customer clicks "Add to Cart" with desired size
5. Cart receives variant ID (e.g., medicine_id: 5, not parent 1)

### Viewing a Standalone Product:

- No variant selector shown
- Normal product display as before
- Add to cart works as usual

### Direct Variant URL:

- If customer tries to access: `/medicines/himalaya-face-wash-10ml`
- Automatically redirects to parent: `/medicines/himalaya-face-wash`
- Variant is auto-selected in the variant selector

## Backend Changes:

### MedicineController Updates:

**Index (Browse Products):**

- Only shows parent products in search results
- Variants are NOT listed separately
- Filters work correctly with parent products

**Show (Product Detail):**

- Accepts both parent and variant IDs
- If variant ID provided, redirects to parent
- Loads parent with all variants for selector
- Shows related products (other parents in same category)

**Suggestions (Search):**

- Only suggests parent products
- Variants filtered out

### CartController Updates:

**Add Method:**

- Accepts optional `variant_id` parameter
- If variant provided, adds that variant to cart (not parent)
- Validates that selected item is not a parent (must have variant)
- Uses `displayName()` which includes variant label
- Stock checks work per-variant

### Database Queries:

```php
// Frontend shows only parents
Medicine::whereNull('parent_medicine_id')

// Related products are also parents
Medicine::whereNull('parent_medicine_id')

// Variants found via parent relationship
$parent->variants() // HasMany relationship
```

## Alpine.js Components:

### variantSelector()

```javascript
// Handles variant button clicks
// Updates selected variant ID in hidden input
// Updates displayed stock and label
```

### priceDisplay()

```javascript
// Manages dynamic pricing display
// Recalculates discount based on selected variant
// Formats prices with rupees and decimals
```

### stockDisplay()

```javascript
// Monitors selected variant
// Updates stock display in real-time
// Handles API calls to fetch variant data
```

## Cart Display:

### Shopping Cart Page:

- Each variant appears as **separate line item**
- Shows variant label in product name (e.g., "Himalaya Face Wash 10 ml")
- Quantity can be adjusted per variant
- Removing one variant doesn't affect others
- Total is calculated correctly across variants

**Example Cart:**

```
Himalaya Face Wash 10 ml     ₹150  × 2 = ₹300
Himalaya Face Wash 50 ml     ₹450  × 1 = ₹450
Dolo 500mg                   ₹35   × 3 = ₹105
─────────────────────────────────────────────
Subtotal:                                ₹855
```

## Order Processing:

### Checkout:

- Each variant maintained as separate OrderItem
- Stock decremented per-variant
- Order history shows specific variant purchased
- Refunds track specific variant

### Order History:

- Shows exact variant customer ordered
- Example: "Himalaya Face Wash 10 ml × 2"
- Allows reordering same variant or different size

## Related Products:

### "You May Also Like" Section:

- Shows other parent products in same category
- Each has its own variant selector if applicable
- Clicking "Add to Cart" on related product shows its variants

## Edge Cases Handled:

✅ **Parent product accessed directly:** Redirects to show variant selector  
✅ **Variant URL accessed:** Redirects to parent, variant auto-selected  
✅ **Add parent without variant:** Error message shown  
✅ **Add variant without parent:** Works correctly (variant is SKU)  
✅ **Out of stock variant:** Stock check per-variant  
✅ **Low stock variant:** Shows accurate count  
✅ **Multiple variants in cart:** Each tracked separately

## API Endpoint (Future):

```
GET /api/medicines/{id}
- Returns medicine/variant details
- Used for real-time price/stock updates
- Used by variant selector
```

## SEO & URLs:

### Parent Product URL:

```
/medicines/himalaya-face-wash
```

### Variant URLs (not used, redirects):

```
/medicines/himalaya-face-wash-10ml    → redirects to parent
/medicines/himalaya-face-wash-100ml   → redirects to parent
```

## Performance Optimization:

- Variant selector uses Alpine.js (no page reload)
- Price display recalculates client-side
- Related products loaded with parent
- No N+1 queries (variants eager-loaded)

## Future Enhancements:

- [ ] Variant comparison mode (side-by-side)
- [ ] Bulk discount for buying multiple sizes
- [ ] Variant-specific promotions
- [ ] Variant images per size
- [ ] Share variant link
- [ ] "Save for later" with variant
- [ ] Variant recommendations based on purchase history
