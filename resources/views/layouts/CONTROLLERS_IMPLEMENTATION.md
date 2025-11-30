# Controllers Implementation Guide - Phase 2

This document outlines the comprehensive controller implementations required for the Gulf eCommerce Platform Phase 2.

## Table of Contents

1. [Customer Controllers](#customer-controllers)
2. [Vendor Controllers](#vendor-controllers)
3. [Admin Controllers](#admin-controllers)
4. [Implementation Order](#implementation-order)
5. [Code Examples](#code-examples)

---

## Customer Controllers

Customer-facing controllers handle user interactions for browsing, purchasing, and account management.

### 1. ProductController (app/Http/Controllers/Customer/ProductController.php)

**Responsibilities:**
- List all products with filtering, sorting, pagination
- Display single product with images, reviews, ratings
- Search functionality with RTL support
- Filter by price, category, vendor, ratings

**Methods:**
- `index()` - List all products
- `show($id)` - Show single product
- `search()` - Search products
- `filter()` - Filter by category, price range, rating

### 2. CartController (app/Http/Controllers/Customer/CartController.php)

**Responsibilities:**
- Add/remove items from cart
- Update quantities
- Apply coupon codes
- Persist cart to database

**Methods:**
- `index()` - Show cart
- `add(Request $request)` - Add item
- `update(Request $request)` - Update quantity
- `remove($id)` - Remove item
- `applyCoupon(Request $request)` - Apply discount

### 3. CheckoutController (app/Http/Controllers/Customer/CheckoutController.php)

**Responsibilities:**
- Checkout flow (address, shipping, payment)
- Order creation
- Payment gateway integration
- Invoice generation

**Methods:**
- `index()` - Show checkout page
- `store(Request $request)` - Create order
- `getShippingOptions()` - Fetch shipping methods
- `selectPaymentMethod()` - Choose payment gateway

### 4. OrderController (app/Http/Controllers/Customer/OrderController.php)

**Responsibilities:**
- View order history
- Track order status
- Handle returns/refunds
- Download invoices

**Methods:**
- `index()` - List customer orders
- `show($id)` - View order details
- `track($id)` - Track shipment
- `requestReturn($id)` - Initiate return
- `downloadInvoice($id)` - Download invoice

### 5. AccountController (app/Http/Controllers/Customer/AccountController.php)

**Responsibilities:**
- User profile management
- Address book
- Wishlist
- Password management

**Methods:**
- `dashboard()` - Account overview
- `editProfile()` - Edit user info
- `updateProfile(Request $request)` - Save changes
- `addresses()` - List addresses
- `addAddress(Request $request)` - Add new address
- `deleteAddress($id)` - Remove address

### 6. WishlistController (app/Http/Controllers/Customer/WishlistController.php)

**Responsibilities:**
- Add/remove from wishlist
- View wishlist
- Share wishlist

**Methods:**
- `index()` - Show wishlist
- `add($productId)` - Add product
- `remove($productId)` - Remove product
- `share(Request $request)` - Share via email

### 7. ReviewController (app/Http/Controllers/Customer/ReviewController.php)

**Responsibilities:**
- Submit product reviews
- Rate products
- View product reviews

**Methods:**
- `store(Request $request)` - Submit review
- `destroy($id)` - Delete own review

---

## Vendor Controllers

Vendor controllers manage vendor-specific operations for shop management.

### 1. VendorDashboardController (app/Http/Controllers/Vendor/DashboardController.php)

**Methods:**
- `index()` - Dashboard with stats
- `getAnalytics()` - Sales, orders, revenue data
- `getTopProducts()` - Best selling products

### 2. VendorProductController (app/Http/Controllers/Vendor/ProductController.php)

**Methods:**
- `index()` - List vendor products
- `create()` - Create new product form
- `store(Request $request)` - Save product
- `edit($id)` - Edit product form
- `update(Request $request, $id)` - Update product
- `destroy($id)` - Delete product
- `uploadImages(Request $request)` - Handle product images

### 3. VendorOrderController (app/Http/Controllers/Vendor/OrderController.php)

**Methods:**
- `index()` - List vendor orders
- `show($id)` - View order details
- `updateStatus(Request $request)` - Change order status
- `generateLabel()` - Generate shipping label

### 4. VendorEarningsController (app/Http/Controllers/Vendor/EarningsController.php)

**Methods:**
- `index()` - Show earnings overview
- `getPaymentHistory()` - View payments
- `requestWithdrawal(Request $request)` - Request payout

### 5. VendorSettingsController (app/Http/Controllers/Vendor/SettingsController.php)

**Methods:**
- `edit()` - Edit vendor settings
- `update(Request $request)` - Save settings
- `bankDetails()` - Manage bank info
- `uploadBankProof(Request $request)` - Upload verification

---

## Admin Controllers

Admin controllers manage administrative operations and platform configuration.

### 1. AdminDashboardController (app/Http/Controllers/Admin/DashboardController.php)

**Methods:**
- `index()` - Admin dashboard with KPIs
- `getSystemStats()` - Platform statistics
- `getRevenueReport()` - Revenue data

### 2. AdminUserController (app/Http/Controllers/Admin/UserController.php)

**Methods:**
- `index()` - List all users
- `show($id)` - View user details
- `updateStatus(Request $request)` - Activate/suspend users
- `delete($id)` - Delete user account

### 3. AdminVendorController (app/Http/Controllers/Admin/VendorController.php)

**Methods:**
- `index()` - List vendors
- `show($id)` - Vendor details
- `approve($id)` - Approve vendor
- `reject(Request $request, $id)` - Reject vendor
- `updateCommission(Request $request)` - Set commission rate

### 4. AdminProductController (app/Http/Controllers/Admin/ProductController.php)

**Methods:**
- `index()` - List products
- `approve($id)` - Approve product
- `reject(Request $request, $id)` - Reject product
- `delete($id)` - Delete product

### 5. AdminOrderController (app/Http/Controllers/Admin/OrderController.php)

**Methods:**
- `index()` - List orders
- `show($id)` - View order
- `refund(Request $request)` - Process refund
- `dispute(Request $request)` - Handle disputes

### 6. AdminReportsController (app/Http/Controllers/Admin/ReportsController.php)

**Methods:**
- `index()` - Reports dashboard
- `sales()` - Sales report
- `users()` - User analytics
- `vendors()` - Vendor performance
- `export(Request $request)` - Export to CSV

---

## Implementation Order

### Phase 2A (Week 1-2):
1. Customer ProductController
2. Customer CartController
3. Customer CheckoutController
4. Customer AccountController

### Phase 2B (Week 3-4):
5. Customer OrderController
6. Customer WishlistController
7. Customer ReviewController
8. All Vendor Controllers (5 total)

### Phase 2C (Week 5-6):
9. All Admin Controllers (6 total)
10. Payment gateway controllers
11. Shipping integration controllers

---

## Code Examples

### ProductController Example

```php
namespace App\Http\Controllers\Customer;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', 'approved');
        
        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', fn($q) => 
                $q->where('slug', $request->category)
            );
        }
        
        // Filter by price
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [
                $request->min_price,
                $request->max_price
            ]);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $products = $query->paginate(12);
        
        return view('products.index', compact('products'));
    }
    
    public function show($id)
    {
        $product = Product::with('images', 'reviews', 'vendor')
            ->findOrFail($id);
        
        return view('products.show', compact('product'));
    }
}
```

### CheckoutController Example

```php
namespace App\Http\Controllers\Customer;

use App\Models\Order;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:paytabs,telr',
            'coupon' => 'nullable|string',
        ]);
        
        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'shipping_address_id' => $validated['shipping_address_id'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'total_amount' => $this->calculateTotal(),
        ]);
        
        // Process payment
        return redirect()->route('payment.process', $order);
    }
}
```

---

## Testing Requirements

Each controller must have:
- Unit tests for business logic
- Feature tests for HTTP requests
- 80%+ code coverage
- Security tests for authentication/authorization

---

## Security Considerations

- All controllers use `auth()` middleware
- Input validation on all user inputs
- CSRF protection on POST/PUT/DELETE
- Rate limiting on checkout and payment endpoints
- SQL injection prevention with Eloquent ORM
- XSS prevention with Blade templating

---

## Performance Optimization

- Use Eloquent eager loading (with, whereHas)
- Cache product listings
- Pagination on large datasets
- Query optimization with indexes
- Database connection pooling

---

**Status:** Ready for implementation
**Priority:** High
**Estimated Timeline:** 6 weeks
