# Gulf eCommerce Platform - Deployment Guide

## Overview

This guide provides instructions for deploying the Gulf eCommerce Platform to production environments.

## Prerequisites

- Linux Server (Ubuntu 20.04+ recommended)
- Docker & Docker Compose
- Git
- SSL Certificate (Let's Encrypt recommended)
- Domain name configured
- Sufficient storage and bandwidth

## Production Deployment Steps

### 1. Server Preparation

```bash
# Update system packages
sudo apt-get update && sudo apt-get upgrade -y

# Install Docker
curl -fsSL https://get.docker.com | sh

# Add user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### 2. Clone & Setup

```bash
# Clone repository
git clone https://github.com/black4me/gulf-ecommerce-platform.git /var/www/gulf-ecommerce
cd /var/www/gulf-ecommerce

# Setup environment
cp .env.example .env

# Edit .env with production values
vim .env
```

### 3. Environment Configuration

Update the following in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_HOST=mysql
DB_DATABASE=production_db
DB_USERNAME=prod_user
DB_PASSWORD=strong_password_here
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 4. Deploy with Docker

```bash
# Build and start containers
docker-compose -f docker-compose.prod.yml up -d

# Install dependencies
docker-compose exec app composer install --no-dev --optimize-autoloader

# Generate application key
docker-compose exec app php artisan key:generate

# Run migrations
docker-compose exec app php artisan migrate --force

# Seed database (if needed)
docker-compose exec app php artisan db:seed --force

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# Create storage link
docker-compose exec app php artisan storage:link
```

### 5. Nginx Configuration

Create `/etc/nginx/sites-available/gulf-ecommerce.conf`:

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    root /var/www/gulf-ecommerce/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.env {
        deny all;
    }
}
```

### 6. SSL Certificate (Let's Encrypt)

```bash
# Install certbot
sudo apt-get install certbot python3-certbot-nginx -y

# Get certificate
sudo certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo systemctl enable certbot.timer
```

### 7. Backup Strategy

```bash
# Create backup script
#!/bin/bash
BACKUP_DIR="/backups/gulf-ecommerce"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
docker-compose exec -T mysql mysqldump -u root -p$DB_PASSWORD $DB_DATABASE > $BACKUP_DIR/db_$DATE.sql

# Backup uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/gulf-ecommerce/storage/uploads

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

### 8. Monitoring & Logging

```bash
# Check logs
docker-compose logs -f app

# View app logs
docker-compose exec app tail -f storage/logs/laravel.log

# Monitor resources
docker stats
```

### 9. Updates & Maintenance

```bash
# Pull latest changes
git pull origin main

# Update dependencies
docker-compose exec app composer update --no-dev

# Run migrations
docker-compose exec app php artisan migrate --force

# Restart services
docker-compose restart
```

## Scaling Considerations

1. **Database**: Use managed database service (AWS RDS, DigitalOcean Managed DB)
2. **Storage**: Use object storage (AWS S3, DigitalOcean Spaces)
3. **Cache**: Use Redis cluster for high availability
4. **Load Balancing**: Use Nginx upstream or cloud load balancer
5. **CDN**: Integrate CloudFlare or AWS CloudFront

## Performance Optimization

1. Enable HTTP/2 and gzip compression
2. Use Redis for caching and sessions
3. Implement rate limiting
4. Optimize database queries
5. Enable lazy loading for images
6. Use a CDN for static assets

## Security Checklist

- [ ] HTTPS enforced
- [ ] Strong database passwords
- [ ] Firewall configured
- [ ] Regular backups
- [ ] Security headers set
- [ ] Rate limiting enabled
- [ ] Input validation implemented
- [ ] SQL injection prevention
- [ ] CSRF protection enabled
- [ ] Log monitoring active

## Troubleshooting

### Container won't start
```bash
docker-compose logs app
```

### Database connection error
```bash
docker-compose exec mysql mysql -u root -p$DB_PASSWORD -e 'SHOW DATABASES;'
```

### Permission issues
```bash
sudo chown -R www-data:www-data /var/www/gulf-ecommerce/storage
sudo chmod -R 775 /var/www/gulf-ecommerce/storage
```

## Support

For deployment issues, contact: devops@gulfecommerce.com
