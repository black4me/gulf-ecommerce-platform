# Gulf eCommerce Platform - Setup Guide

## Prerequisites
- Docker and Docker Compose
- Git
- PHP 8.1+ (for local development)
- Composer
- Node.js 16+

## Quick Start with Docker

### 1. Clone the Repository
```bash
git clone https://github.com/black4me/gulf-ecommerce-platform.git
cd gulf-ecommerce-platform
```

### 2. Environment Configuration
```bash
cp .env.example .env
```
Edit `.env` file with your configuration values.

### 3. Start Services
```bash
docker-compose up -d
```

### 4. Install Dependencies
```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

### 5. Setup Database
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### 6. Generate Keys
```bash
docker-compose exec app php artisan key:generate
```

### 7. Access Application
- Frontend: http://localhost:8080
- Admin: http://localhost:8080/admin
- Database: localhost:3306

## Local Development (Without Docker)

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 4. Run Server
```bash
php artisan serve
```

## Project Structure

- `app/` - Application logic
- `config/` - Configuration files
- `database/` - Database migrations and seeders
- `resources/` - Views, CSS, JS
- `routes/` - Application routes
- `storage/` - Logs, uploads
- `tests/` - Test files
- `docker/` - Docker configuration

## Key Features

- Multi-Vendor eCommerce Platform
- Multi-Language Support (Arabic RTL + English)
- Multiple Currencies (GCC + USD)
- Payment Gateway Integration
- Shipping Integration
- Admin Dashboard
- Vendor Dashboard
- Customer Portal

## Development Workflow

1. Create feature branch: `git checkout -b feature/your-feature`
2. Make changes and commit: `git commit -am 'Add feature'`
3. Push to remote: `git push origin feature/your-feature`
4. Create pull request

## Testing

```bash
docker-compose exec app php artisan test
```

## Troubleshooting

### Port Already in Use
```bash
# Change ports in docker-compose.yml
# Or stop existing containers
docker-compose down
```

### Database Connection Error
```bash
# Ensure MySQL is running and accessible
docker-compose exec mysql mysql -u root -p
```

## Support
For issues and questions, please create an issue on GitHub.
