# Complete Product Variants System - Setup & Usage Guide

## ✅ What's Been Implemented

A complete parent-child product variant system allowing products to have different sizes, strengths, and volumes tracked independently.

### Example:

```
Himalaya Face Wash (Parent - ID: 1)
├─ 10 ml (Variant - ID: 2, Stock: 50)
├─ 20 ml (Variant - ID: 3, Stock: 100)
├─ 50 ml (Variant - ID: 4, Stock: 200)
└─ 100 ml (Variant - ID: 5, Stock: 150)
```

---

## 📊 Database Schema

### New Columns in `medicines` Table:

```sql
parent_medicine_id (nullable FK) -- Links to parent product
variant_label (string)           -- Display text (e.g., "10 ml")
variant_value (float)            -- Numeric value for sorting
variant_unit (string)            -- Unit (ml, mg, tabs, etc.)
```

### Migration:

File: `database/migrations/2026_06_11_083748_add_variant_fields_to_medicines_table.php`
Status: ✅ Applied

---

## 🎛️ Admin Panel Features

### Location: Admin → Medicines

#### Create Parent Product:

1. Click "Add Medicine"
2. Fill all fields normally
3. Leave "This is a product variant" **unchecked**
4. Save

#### Create Variants of Existing Product:

1. Open parent product in **Edit**
2. Scroll to **"Variant Fields"** section (amber box)
3. Check: "This is a product variant"
4. Fill:
    - **Variant Label**: Auto-generated from Value + Unit
    - **Value**: e.g., 10, 20, 50, 100
    - **Unit**: Select from dropdown
5. Set pricing and stock specific to this variant
6. Save

#### Admin Medicines List:

- **Parent products** show ▶ arrow (expandable)
- **Variants** shown indented under parent when expanded
- **Variant label** displayed in blue
- **Total stock** shown for parent (sum of all variants)
- **Individual stock** shown per variant

**Actions:**

- Edit parent/variant separately
- Delete individual variants
- Delete parent (cascades to all variants)

---

## 🛍️ Customer Frontend

### Product Browse Page:

- Only **parent products** appear in search results
- Variants are **not** listed separately
- Search works across parent names and descriptions

### Product Detail Page:

When viewing a parent product:

1. **Variant Selector** (below product name):
    - Shows buttons for each variant
    - Example: [10 ml] [20 ml] [50 ml] [100 ml]
    - First variant auto-selected
    - Click to switch sizes

2. **Dynamic Display**:
    - Price updates for selected variant
    - Stock updates for selected variant
    - Discount % updates if applicable
    - "You save" amount updates

3. **Add to Cart**:
    - Automatically uses selected variant
    - Cannot add parent without selecting variant
    - Adds variant to cart (not parent)

### Shopping Cart:

- Each variant shown as **separate line item**
- Displays: "Himalaya Face Wash 10 ml"
- Quantity adjustable per variant
- Total calculated across all variants

### Order History:

- Shows exact variant purchased
- Example: "Himalaya Face Wash 10 ml × 2"
- Allows reordering with variant choice

---

## 💻 Model Methods

### Medicine Model Methods:

```php
// Check if product is a parent
$medicine->isParent()              // bool

// Check if product is a variant (child)
$medicine->isVariant()             // bool

// Get all variants
$medicine->variants()              // HasMany relationship

// Get parent
$medicine->parent()                // BelongsTo relationship

// Get display name with variant label
$medicine->displayName()           // string

// Get total stock (parents only)
$medicine->totalStock()            // int
```

### Usage Example:

```php
$product = Medicine::find(1);

if ($product->isParent()) {
    // This is a parent product
    foreach ($product->variants() as $variant) {
        echo "{$variant->displayName()}: ₹{$variant->priceRupees()}";
    }
    echo "Total stock: {$product->totalStock()}";
}
```

---

## 🔄 Data Flow

### Creating a Product with Variants:

```
Admin Form (Add Medicine)
    ↓
Check "Is Variant?" → NO
Fill parent details
    ↓
Store with parent_medicine_id = NULL
    ↓
Variant Fields hidden
    ↓
Variant saved as parent
```

### Creating Variant of Parent:

```
Admin Form (Edit Parent)
    ↓
Check "Is Variant?" → YES
Variant Fields shown
Fill variant label, value, unit
Adjust pricing/stock for variant
    ↓
Store with parent_medicine_id = <parent-id>
    ↓
Variant saved as child of parent
```

### Customer Purchasing:

```
Product Detail Page (Parent)
    ↓
Variant Selector available
Customer clicks size button
    ↓
Price/stock updated for that variant
    ↓
Add to Cart button uses variant ID
    ↓
Variant added to cart (not parent)
    ↓
Cart shows specific variant purchased
```

---

## 🎯 Key Features

### ✅ Parent Products:

- Primary product entry
- Has category, manufacturer, description
- Shows total stock across variants
- Links to all child variants
- Displayed in search results

### ✅ Child Variants:

- Cannot exist without parent
- Has own pricing (can differ from parent)
- Has own stock (tracked separately)
- Has variant label (size/strength display)
- Cannot be searched directly (only parent)
- Cannot be variant of variant (max 2 levels)

### ✅ Stock Management:

- Each variant has independent stock
- Parent shows total stock
- Stock decrements per-variant on purchase
- Low stock warnings per-variant
- Out of stock variants prevent cart add

### ✅ Pricing:

- Variants can have different MRP/prices
- Discount calculated per-variant
- Cart shows variant-specific price
- Order maintains variant price snapshot

### ✅ Images:

- Variants can share parent images or have unique ones
- Each variant can have different primary/extra images
- Falls back to parent images if not set

---

## 📋 API Changes

### MedicineController:

**index()** - Browse

- Filters: `whereNull('parent_medicine_id')`
- Only shows parents
- Variants excluded from listing

**show()** - Detail

- Accepts parent or variant ID
- If variant ID: redirects to parent
- Loads all variants for selector
- Shows related parents only

**suggestions()** - Search

- Filters: `whereNull('parent_medicine_id')`
- Only suggests parents

### CartController:

**add()** - Add to Cart

- Accepts: `medicine_id` and optional `variant_id`
- Uses variant_id if provided
- Prevents adding parent without variant
- Uses `displayName()` for cart text
- Stock checks per-variant

---

## ⚠️ Important Notes

### For Admin:

✅ **Do:**

- Create parents first, then variants
- Set meaningful variant labels
- Use consistent units (ml, mg, tabs)
- Set pricing appropriately per variant
- Track stock per variant

❌ **Don't:**

- Delete parent if variants exist (cascades)
- Create variant of variant
- Leave variant label empty
- Use inconsistent units

### For Customers:

✅ **Do:**

- Select desired size before adding to cart
- Check stock for specific variant
- Review price for selected size
- Compare variants with different prices

❌ **Don't:**

- Add parent product (must select variant first)
- Try to access variant directly (redirects to parent)
- Mix variant editions in confused cart

---

## 🚀 Usage Workflow

### Step 1: Create Parent Product

```
Admin → Medicines → Add Medicine
- Name: "Himalaya Face Wash"
- Manufacturer: "Himalaya"
- Category: "Face Wash"
- Description: "Natural ingredients..."
- Primary Image: himalaya-facewash.jpg
- Pricing: MRP ₹500, Price ₹399
- Stock: 0 (will track via variants)
- Is Variant: NO ← Important!
Save → Parent created as ID: 1
```

### Step 2: Create Variants

```
Admin → Medicines → Edit (ID: 1)
Check: "This is a product variant"

Variant 1:
- Label: "10 ml"
- Value: 10
- Unit: ml
- MRP: ₹100, Price: ₹79
- Stock: 50
Save → Variant created as ID: 2

Repeat for: 20 ml (ID: 3), 50 ml (ID: 4), 100 ml (ID: 5)
```

### Step 3: Customer Views Product

```
Customer visits: /medicines/himalaya-face-wash
- Sees: Himalaya Face Wash, by Himalaya
- Variant Selector: [10 ml] [20 ml] [50 ml] [100 ml]
- First variant (10 ml) selected
- Price: ₹79, MRP: ₹100, Stock: 50
- Can click other sizes to update price/stock
- Clicks "Add to Cart" with desired size
- Cart receives variant ID (2, 3, 4, or 5)
```

---

## 📚 Documentation Files

1. **VARIANTS_GUIDE.md** - Admin guide for creating variants
2. **FRONTEND_VARIANTS.md** - Customer-facing variant features
3. **COMPLETE_VARIANTS_SETUP.md** - This file (full setup guide)

---

## 🔧 Technical Implementation

### Files Modified:

**Backend:**

- `app/Models/Medicine.php` - Added relationships and methods
- `app/Http/Controllers/Admin/AdminMedicineController.php` - Variant form fields
- `app/Http/Controllers/MedicineController.php` - Frontend filtering
- `app/Http/Controllers/CartController.php` - Variant handling
- `resources/views/admin/medicines/_form.blade.php` - Variant input fields
- `resources/views/admin/medicines/index.blade.php` - Expandable variants
- `resources/views/medicines/show.blade.php` - Variant selector

**Database:**

- `database/migrations/2026_06_11_083748_add_variant_fields_to_medicines_table.php`

### Technologies Used:

- Laravel 11 (Models, Relationships, Migrations)
- Alpine.js (Client-side variant selection)
- Blade Templating (Admin & Frontend views)
- MySQL (Self-referential FK)

---

## ✨ Future Enhancements

- [ ] Variant comparison UI
- [ ] Bulk discount for multiple sizes
- [ ] Variant-specific promotions
- [ ] Recommended size based on usage
- [ ] Variant subscription option
- [ ] Size conversion calculator
- [ ] Variant reviews separately
- [ ] "Subscribe & Save" per variant
- [ ] Variant image gallery per size
- [ ] Variant availability calendar

---

## ❓ FAQ

**Q: Can I have multiple variants of one product?**
A: Yes, unlimited variants per parent.

**Q: What if I want to change a variant to a parent?**
A: Not supported directly. Create new parent instead.

**Q: Can variants have different images?**
A: Yes, each variant can have unique images or share parent images.

**Q: How is stock tracked?**
A: Each variant has independent stock. Parent shows total.

**Q: Can customers order variants separately?**
A: Yes, each variant is a separate cart item with own pricing/stock.

**Q: What if parent is deleted?**
A: All variants are cascaded deleted (automatic).

---

## 🎉 You're All Set!

The product variants system is fully implemented and ready to use. Create your first parent product with variants and watch them appear on the customer-facing store!

For questions or issues, refer to the relevant documentation file above.
