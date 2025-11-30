# Gulf eCommerce Platform - Testing Guide

## دليل الاختبار الشامل

### المقدمة
هذا الدليل يوضح كيفية اختبار جميع مكونات المنصة في جلسة التطوير الأولى.

---

## 1. اختبار المسارات (Routes Testing)

### Web Routes
```bash
# اختبر تحميل الصفحة الرئيسية
GET / -> يجب أن يعيد 404 ولا يوجد views بعد (متوقع)
GET /ar -> يجب أن يعيد 404 ولا يوجد views بعد (متوقع)
GET /en -> يجب أن يوجد 404 ولا يوجد views بعد (متوقع)
```

### API Routes
```bash
# Health check
GET /api/v1/health
# Expected Response: 200 OK
{
  "status": "ok",
  "message": "API is running",
  "timestamp": "2025-11-30T23:00:00Z"
}
```

---

## 2. اختبار Authentication API

### تسجيل مستخدم جديد
```bash
POST /api/v1/auth/register

Request Body:
{
  "name": "علي محمد",
  "email": "ali@example.com",
  "password": "password123",
  "role": "customer"
}

Expected Response: 201 Created
{
  "success": true,
  "message": "تم إنشاء الحساب بنجاح",
  "data": {
    "user": { ... },
    "token": "eyJhbGc...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

### تسجيل الدخول
```bash
POST /api/v1/auth/login

Request Body:
{
  "email": "ali@example.com",
  "password": "password123"
}

Expected Response: 200 OK
{
  "success": true,
  "message": "تم الدخول بنجاح",
  "data": { ... }
}
```

---

## 3. اختبار OrderService

### حساب ملخص الطلب
```php
$items = collect([
    ['quantity' => 2, 'unit_price' => 100],
    ['quantity' => 1, 'unit_price' => 50],
]);

$summary = (new OrderService())->calculateOrderSummary($items, 20, 0.15);

// Expected:
// subtotal: 250
// tax_amount: 37.50
// shipping_cost: 20
// total_amount: 307.50
```

### إنشاء طلب
```php
$order = (new OrderService())->createOrder(
    userId: 1,
    orderData: [
        'subtotal' => 250,
        'tax_amount' => 37.50,
        'shipping_cost' => 20,
        'discount_amount' => 0,
        'total_amount' => 307.50,
        'currency' => 'SAR',
        'items' => [
            [
                'product_id' => 1,
                'vendor_id' => 1,
                'quantity' => 2,
                'unit_price' => 100,
                'total_price' => 200,
            ]
        ]
    ]
);

// Expected: Order with order_number like ORD20251130230012345
```

---

## 4. اختبار التعريب (Localization)

### اختبار اللغة العربية
```bash
# يجب أن تدعم المسارات /ar وتحتوي على RTL
GET /ar/products
# Headers يجب أن تحتوي على:
# Accept-Language: ar
# Content-Language: ar
```

### اختبار العملات المتعددة
```bash
GET /api/v1/products
# يجب أن تدعم query parameter:
# ?currency=SAR أو AED أو KWD إلخ
```

---

## 5. اختبار Multi-Vendor

### الحصول على طلبات البائع
```bash
GET /api/v1/vendor/orders

Expected Response: List of vendor's orders
[
  {
    "id": 1,
    "product_id": 1,
    "vendor_id": 1,
    "quantity": 2,
    "status": "pending"
  }
]
```

### تحديث حالة المنتج
```bash
PUT /api/v1/vendor/products/{id}/status

Request Body:
{
  "status": "active"
}

Expected Response: 200 OK - Product status updated
```

---

## 6. اختبار Payment Processing

### معالجة الدفع
```bash
POST /api/v1/payments/process

Request Body:
{
  "order_id": 1,
  "amount": 307.50,
  "currency": "SAR",
  "method": "credit_card",
  "gateway": "paytabs"
}

Expected Response: 200 OK
{
  "success": true,
  "payment_id": 1,
  "reference_id": "PAY202511302300..."
}
```

---

## 7. قائمة فحص الاختبار (Test Checklist)

### Authentication ✓
- [ ] تسجيل مستخدم جديد
- [ ] تسجيل الدخول
- [ ] التحقق من Token
- [ ] تحديث الملف الشخصي
- [ ] تسجيل الخروج

### Products ✓
- [ ] الحصول على قائمة المنتجات
- [ ] البحث عن منتج
- [ ] تصفية حسب الفئة
- [ ] تصفية حسب السعر
- [ ] الحصول على تفاصيل المنتج

### Orders ✓
- [ ] إنشاء طلب جديد
- [ ] الحصول على الطلبات
- [ ] تحديث حالة الطلب
- [ ] إلغاء الطلب
- [ ] طلب استرجاع أموال

### Cart ✓
- [ ] إضافة منتج للسلة
- [ ] تحديث الكمية
- [ ] حذف منتج من السلة
- [ ] حساب الإجمالي
- [ ] تطبيق كود خصم

### Payments ✓
- [ ] معالجة الدفع
- [ ] التحقق من الدفع
- [ ] رفع الفاتورة
- [ ] استرجاع الأموال

### Vendor ✓
- [ ] لوحة البائع
- [ ] إضافة منتج
- [ ] تحديث المنتج
- [ ] إدارة الطلبات
- [ ] عرض العمولات

---

## 8. اختبارات الأداء (Performance Testing)

```bash
# محاكاة 100 مستخدم متزامن
Loading Time Target: < 3 seconds
Database Query Time: < 500ms
API Response Time: < 200ms

# نتائج متوقعة:
✅ جميع الطلبات يجب أن تكتمل بنجاح
✅ لا يجب أن يكون هناك timeout
✅ معدل الخطأ = 0%
```

---

## 9. اختبارات الأمان (Security Testing)

```bash
# SQL Injection Test
GET /api/v1/products?search=1' OR '1'='1
Expected: Safe - parametrized query

# CSRF Test
POST /api/v1/orders
Without CSRF Token
Expected: 419 Unprocessable Entity

# Authorization Test
GET /api/v1/vendor/orders
Without Authentication
Expected: 401 Unauthorized
```

---

## 10. أدوات الاختبار الموصى بها

### API Testing
- **Postman:** لاختبار الـ Endpoints
- **Insomnia:** بديل Postman
- **cURL:** من سطر الأوامر

### Performance Testing
- **Apache JMeter:** لاختبارات الحمل
- **Locust:** محاكاة المستخدمين
- **Artillery:** اختبارات الأداء

### Security Testing
- **OWASP ZAP:** اختبارات الأمان
- **Burp Suite:** تحليل الأمان

---

## 11. أمثلة cURL للاختبار

```bash
# Health Check
curl -X GET http://localhost:8000/api/v1/health

# User Registration
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"علي","email":"ali@test.com","password":"test123"}'

# Get Products
curl -X GET http://localhost:8000/api/v1/products?currency=SAR

# Create Order
curl -X POST http://localhost:8000/api/v1/orders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"items":[...]}'
```

---

## 12. النتائج المتوقعة

✅ جميع الاختبارات يجب أن تنجح
✅ لا يجب أن يكون هناك أخطاء غير معالجة
✅ جميع الاستجابات يجب أن تتبع معايير JSON
✅ الأداء يجب أن يكون ممتازاً (< 200ms)
✅ الأمان يجب أن يكون محمياً بالكامل

---

## الخلاصة

بعد إكمال جميع الاختبارات في هذا الدليل، يكون المشروع جاهزاً للانتقال إلى المرحلة الثانية من التطوير.

**Happy Testing! 🚀**
