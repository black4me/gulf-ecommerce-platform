# Gulf eCommerce Platform - System Architecture

## High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   Client Layer                           │
│  Web Browser (Customer) | Mobile App | Admin Dashboard  │
└─────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────┐
│              CDN / Static Assets (CloudFlare)           │
└─────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────┐
│                    Load Balancer                         │
│                  (Nginx / AWS ALB)                       │
└─────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────┐
│                 Web Application Layer                    │
│    ┌────────────────────────────────────────────────┐   │
│    │  Laravel 10 + Bagisto + Multi-Vendor          │   │
│    │  - API Controllers                             │   │
│    │  - Web Controllers                             │   │
│    │  - Middleware                                  │   │
│    └────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────┐
│              Business Logic Layer (Services)             │
│  ┌──────────────┬──────────────┬──────────────────────┐ │
│  │ Order        │ Payment      │ Shipping             │ │
│  │ Management   │ Processing   │ Management           │ │
│  ├──────────────┼──────────────┼──────────────────────┤ │
│  │ Vendor       │ Product      │ Customer             │ │
│  │ Management   │ Management   │ Management           │ │
│  └──────────────┴──────────────┴──────────────────────┘ │
└─────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────┐
│           Data Access & ORM Layer (Eloquent)             │
│    - Models with relationships                           │
│    - Query builders                                      │
│    - Event listeners                                     │
└─────────────────────────────────────────────────────────┘
                              ↓
         ┌────────────────────┬────────────────┐
         ↓                    ↓                ↓
   ┌─────────────┐    ┌─────────────┐  ┌──────────────┐
   │  MySQL 8.0  │    │Redis Cache  │  │Object Storage│
   │  (Database) │    │  (Sessions) │  │(AWS S3/CDN)  │
   └─────────────┘    └─────────────┘  └──────────────┘
```

## System Components

### 1. Frontend Layer
- **Web Application:** Vue.js 3 + Blade Templates
- **Mobile App:** Flutter (future)
- **Admin Panel:** Custom Vue components
- **Vendor Dashboard:** Role-based access control

### 2. API Layer
- **REST API:** RESTful endpoints for mobile/frontend
- **Authentication:** JWT / Sanctum tokens
- **Rate Limiting:** Request throttling per role
- **Versioning:** API v1, v2 support

### 3. Business Logic Layer
```
Services/
├── OrderService
│   ├── Create order
│   ├── Update status
│   ├── Process refunds
│   └── Generate invoice
├── PaymentService
│   ├── PayTabs integration
│   ├── Telr integration
│   ├── Stripe integration
│   └── Payment verification
├── ShippingService
│   ├── Aramex integration
│   ├── SMSA integration
│   ├── Rate calculation
│   └── Tracking
└── VendorService
    ├── Commission calculation
    ├── Payout processing
    ├── Performance analytics
    └── Account management
```

### 4. Data Layer
```
Models/
├── User (Customers)
├── Admin
├── Vendor
├── Product
├── Order
├── OrderItem
├── Payment
├── Shipment
├── Review
└── Cart
```

## Database Schema (Key Tables)

```sql
users                  -- Customers/Users
admins                 -- Admin accounts
vendors                -- Vendor profiles
products               -- Product listings
order_items            -- Line items in orders
orders                 -- Customer orders
payments               -- Payment transactions
shipments              -- Shipping tracking
reviews                -- Product reviews
vendor_commissions     -- Commission tracking
```

## Technology Stack

### Backend
- **Framework:** Laravel 10
- **Database:** MySQL 8.0
- **Cache:** Redis
- **Queue:** Redis (for async jobs)
- **PHP Version:** 8.2+

### Frontend
- **Framework:** Vue.js 3
- **Styling:** Tailwind CSS + SCSS
- **Build Tool:** Vite / Laravel Mix
- **Template Engine:** Blade

### Infrastructure
- **Containerization:** Docker
- **Orchestration:** Docker Compose (dev), Kubernetes (prod)
- **Web Server:** Nginx
- **Reverse Proxy:** Nginx Load Balancer
- **CDN:** CloudFlare
- **Object Storage:** AWS S3

## API Design

### Authentication
```
POST /api/v1/auth/login
POST /api/v1/auth/register
POST /api/v1/auth/refresh
POST /api/v1/auth/logout
```

### Products
```
GET    /api/v1/products
GET    /api/v1/products/{id}
GET    /api/v1/products/vendor/{vendor_id}
```

### Orders
```
POST   /api/v1/orders
GET    /api/v1/orders/{id}
GET    /api/v1/orders (user's orders)
PUT    /api/v1/orders/{id}/status
```

### Payments
```
POST   /api/v1/payments
GET    /api/v1/payments/{id}
POST   /api/v1/payments/{id}/verify
```

## Security Architecture

1. **Authentication:**
   - JWT tokens with 1-hour expiry
   - Refresh tokens with 30-day expiry
   - Role-based access control (RBAC)

2. **Data Protection:**
   - HTTPS/TLS 1.3
   - Password hashing (bcrypt)
   - Input validation & sanitization
   - SQL injection prevention (ORM)

3. **Infrastructure Security:**
   - Firewall rules
   - DDoS protection (CloudFlare)
   - Regular security audits
   - Automated backups

## Performance Optimization

1. **Caching Strategy:**
   - Redis for sessions
   - Query result caching
   - HTTP caching headers
   - CDN for static assets

2. **Database Optimization:**
   - Indexed queries
   - Connection pooling
   - Query optimization
   - Read replicas (future)

3. **Code Optimization:**
   - Lazy loading
   - Code splitting
   - Compression (gzip)
   - Minification

## Scalability Considerations

1. **Horizontal Scaling:**
   - Load balancer for multiple app instances
   - Stateless application design
   - Shared session store (Redis)

2. **Vertical Scaling:**
   - Managed database (AWS RDS)
   - Auto-scaling groups
   - Resource monitoring

3. **Database Scaling:**
   - Read replicas
   - Database sharding (future)
   - Query optimization
