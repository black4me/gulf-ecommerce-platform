# Frontend Assets Implementation Guide - Phase 2

Comprehensive guide for CSS, JavaScript, and Vue components for the Gulf eCommerce Platform.

## CSS File Structure

### 1. resources/css/app.css (Main Stylesheet)

**Global Styles:**
- Typography: Font families (both Arabic and English fonts)
- Color scheme: Primary colors (main brand colors)
- Button styles: All button types (primary, secondary, danger, success)
- Form inputs: Text fields, selects, checkboxes, radio buttons
- Alerts and notifications
- Cards and panels
- Spacing utilities (margin, padding)
- Flex and grid utilities

**Content:**
```css
:root {
    --primary: #0066cc;
    --secondary: #f0f0f0;
    --success: #28a745;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #17a2b8;
    --dark: #333;
    --light: #f8f9fa;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    line-height: 1.6;
    color: #333;
}

[dir="rtl"] body {
    font-family: 'Droid Arabic Kufi', 'Traditional Arabic', sans-serif;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: var(--primary);
    color: white;
}

.btn-primary:hover {
    background-color: #0052a3;
}
```

### 2. resources/css/rtl.css (RTL Support)

**Content:**
```css
/* RTL-specific styles */
[dir="rtl"] {
    text-align: right;
}

[dir="rtl"] .container {
    direction: rtl;
    unicode-bidi: bidi-override;
}

[dir="rtl"] .row {
    flex-direction: row-reverse;
}

[dir="rtl"] .col-md-3 {
    margin-right: auto;
    margin-left: 0;
}

[dir="rtl"] .navbar-brand {
    margin-right: 0;
    margin-left: auto;
}

[dir="rtl"] .list-group-item {
    border-right: 1px solid #ddd;
    border-left: none;
    padding-right: 20px;
    padding-left: 12px;
}

[dir="rtl"] .sidebar {
    right: 0;
    left: auto;
}
```

### 3. resources/css/responsive.css (Mobile & Tablet)

**Content:**
```css
/* Tablet (768px and up) */
@media (max-width: 768px) {
    .navbar-collapse {
        background-color: #f8f9fa;
        padding: 10px 0;
    }
    
    .sidebar {
        display: none;
    }
    
    .main-content {
        width: 100%;
        margin-left: 0 !important;
    }
}

/* Mobile (480px and down) */
@media (max-width: 480px) {
    .container {
        padding: 10px;
    }
    
    .btn {
        padding: 8px 15px;
        font-size: 12px;
    }
    
    .product-grid {
        grid-template-columns: 1fr;
    }
}
```

### 4. resources/css/dashboard.css (Dashboard Styling)

**Content:**
```css
.dashboard {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 20px;
    flex-shrink: 0;
}

.main-content {
    flex: 1;
    overflow-y: auto;
}

.card {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}
```

## JavaScript Modules

### 1. resources/js/app.js (Entry Point)

**Content:**
```javascript
// Import Vue 3 if using Vue
import { createApp } from 'vue';

// Import utilities
import './utils/ajax.js';
import './utils/validation.js';
import './utils/cart.js';

// Initialize Bootstrap
import 'bootstrap';

// Global event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application loaded');
    initializeApp();
});

function initializeApp() {
    // Initialize tooltips, popovers, etc.
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => 
        new bootstrap.Tooltip(tooltipTriggerEl)
    );
}
```

### 2. resources/js/utils/cart.js (Shopping Cart)

**Functions:**
- `addToCart(productId, quantity)` - Add item to cart
- `removeFromCart(cartItemId)` - Remove item
- `updateQuantity(cartItemId, quantity)` - Update quantity
- `getCartTotal()` - Calculate total
- `applyDiscount(couponCode)` - Apply coupon
- `getCartItems()` - Fetch cart data

**Example:**
```javascript
export function addToCart(productId, quantity = 1) {
    fetch('/api/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity,
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item added to cart', 'success');
            updateCartCount();
        }
    })
    .catch(error => console.error('Error:', error));
}
```

### 3. resources/js/utils/ajax.js (AJAX Helpers)

**Functions:**
- `fetchData(url, options)` - GET request
- `postData(url, data)` - POST request
- `handleResponse(response)` - Process response
- `handleError(error)` - Error handling

### 4. resources/js/utils/validation.js (Form Validation)

**Functions:**
- `validateEmail(email)` - Email validation
- `validatePhone(phone)` - Phone validation
- `validateForm(formElement)` - Full form validation
- `showValidationErrors(errors)` - Display errors

## Vue Components (if using Vue 3)

### 1. resources/js/components/ProductCard.vue

**Features:**
- Product image gallery
- Price display with currency
- Rating display
- Add to cart button
- Wishlist toggle
- Vendor information

### 2. resources/js/components/CartPanel.vue

**Features:**
- Display cart items
- Update quantities
- Remove items
- Apply coupons
- Checkout button
- Estimated total

### 3. resources/js/components/FilterPanel.vue

**Features:**
- Category filter
- Price range slider
- Rating filter
- Vendor filter
- Apply/reset filters

## Asset Compilation

### Laravel Mix Configuration (webpack.mix.js)

```javascript
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('postcss-import'),
       require('tailwindcss'),
       require('autoprefixer'),
   ])
   .postCss('resources/css/rtl.css', 'public/css')
   .postCss('resources/css/responsive.css', 'public/css')
   .postCss('resources/css/dashboard.css', 'public/css');

if (mix.inProduction()) {
    mix.minify('public/js/app.js')
       .minify('public/css/app.css');
}
```

## Build & Deployment

### Development
```bash
npm run dev
```

### Production
```bash
npm run production
```

### Watch for Changes
```bash
npm run watch
```

## Performance Optimization

1. **CSS Minification:** Enabled in production
2. **JavaScript Bundling:** Tree-shaking enabled
3. **Image Optimization:** Lazy loading, responsive images
4. **Font Loading:** Preload critical fonts
5. **Caching:** Browser caching headers
6. **CDN:** Serve static assets from CDN

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## RTL Considerations

- All margins/padding flipped for RTL
- Text alignment adjusted
- Flex direction reversed
- Border directions adjusted
- Transform origins handled correctly

## Accessibility (WCAG 2.1 AA)

- Semantic HTML structure
- ARIA labels on interactive elements
- Color contrast ratios >= 4.5:1
- Keyboard navigation support
- Focus visible states
- Screen reader compatibility

---

**Status:** Ready for implementation
**Priority:** High
**Estimated Timeline:** 2-3 weeks
