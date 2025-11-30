# Gulf eCommerce Platform - Project Structure

## Directory Layout

```
gulf-ecommerce-platform/
├── app/                          # Application code
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Admin controllers
│   │   │   ├── Shop/            # Customer facing controllers
│   │   │   ├── Vendor/          # Vendor dashboard controllers
│   │   │   └── Api/             # REST API controllers
│   │   ├── Requests/            # Form requests & validation
│   │   └── Resources/           # API resources
│   ├── Models/                  # Eloquent models
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── Vendor.php
│   │   ├── Customer.php
│   │   └── ...
│   ├── Services/                # Business logic
│   │   ├── PaymentService.php
│   │   ├── ShippingService.php
│   │   ├── VendorService.php
│   │   └── ...
│   ├── Repositories/            # Data access layer
│   └── Events/                  # Event classes
│
├── config/                       # Configuration files
│   ├── app.php
│   ├── database.php
│   ├── bagisto.php
│   ├── payment.php              # Payment gateway config
│   └── shipping.php             # Shipping config
│
├── database/
│   ├── migrations/              # Database migrations
│   ├── seeders/                 # Database seeders
│   └── factories/               # Model factories
│
├── resources/
│   ├── views/
│   │   ├── layouts/             # Base layouts
│   │   ├── shop/                # Customer pages
│   │   ├── admin/               # Admin pages
│   │   ├── vendor/              # Vendor pages
│   │   └── emails/              # Email templates
│   ├── css/
│   │   ├── app.scss
│   │   ├── rtl.scss             # RTL support for Arabic
│   │   └── admin.scss
│   ├── js/
│   │   ├── app.js
│   │   ├── components/          # Vue components
│   │   └── store/               # Vuex store
│   └── lang/
│       ├── ar/                  # Arabic translations
│       └── en/                  # English translations
│
├── routes/
│   ├── web.php                  # Web routes
│   ├── api.php                  # API routes
│   ├── admin.php                # Admin routes
│   └── vendor.php               # Vendor routes
│
├── tests/
│   ├── Unit/                    # Unit tests
│   ├── Feature/                 # Feature tests
│   └── TestCase.php
│
├── storage/
│   ├── uploads/                 # User uploads
│   ├── logs/                    # Application logs
│   └── cache/                   # Cache files
│
├── docker/
│   ├── nginx/
│   │   └── nginx.conf           # Nginx configuration
│   ├── php/
│   │   └── php.ini              # PHP configuration
│   └── mysql/
│       └── my.cnf               # MySQL configuration
│
├── .github/
│   └── workflows/
│       └── ci.yml               # GitHub Actions CI/CD
│
├── public/
│   ├── index.php                # Application entry point
│   ├── css/                     # Compiled CSS
│   └── js/                      # Compiled JS
│
├── .env.example                 # Environment template
├── .editorconfig                # Code style
├── .gitignore                   # Git ignore rules
├── composer.json                # PHP dependencies
├── package.json                 # Node.js dependencies
├── docker-compose.yml           # Docker compose configuration
├── Dockerfile                   # Docker build file
├── SETUP.md                     # Setup guide
├── CONTRIBUTING.md              # Contributing guidelines
├── PROJECT_STRUCTURE.md         # This file
└── README.md                    # Project README
```

## Key Directories Description

### /app
Contains all application logic:
- **Controllers**: Handle HTTP requests
- **Models**: Represent database tables
- **Services**: Business logic and reusable operations
- **Repositories**: Data access abstraction

### /config
Configuration files for the application:
- Database connections
- Payment gateway credentials
- Shipping provider settings
- Email configuration

### /database
Database-related files:
- **migrations**: Create/modify database schema
- **seeders**: Populate database with initial data
- **factories**: Generate fake data for testing

### /resources
Public-facing resources:
- **views**: Blade templates for rendering HTML
- **css**: Stylesheets (SCSS with RTL support)
- **js**: Vue components and scripts
- **lang**: Multi-language translation files

### /routes
Application routing definitions:
- Web routes for customers
- API routes for mobile apps
- Admin routes for management
- Vendor routes for sellers

### /docker
Docker configuration for containerization

### /tests
Automated tests (Unit, Feature, Integration)

## File Naming Conventions

- **Controllers**: `ProductController.php` (StudlyCaps)
- **Models**: `Product.php` (StudlyCaps)
- **Services**: `ProductService.php` (StudlyCaps)
- **Views**: `product.blade.php` (snake_case)
- **Migrations**: `2024_11_30_120000_create_products_table.php`
- **Tests**: `ProductTest.php` (StudlyCaps with Test suffix)
