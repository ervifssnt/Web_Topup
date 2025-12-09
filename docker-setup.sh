#!/bin/sh

# Docker Setup Script for Laravel Game Top-Up Application
# This script handles initial Docker setup with proper permissions

set -e

echo "🚀 Starting Laravel Game Top-Up Docker Setup..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Error: Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if docker compose is available
if ! command -v docker compose &> /dev/null; then
    echo "❌ Error: docker compose command not found. Please install Docker Compose."
    exit 1
fi

echo "✅ Docker is running"

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file from .env.docker.example..."
    cp .env.docker.example .env
    echo "✅ .env file created"
else
    echo "✅ .env file already exists"
fi

# Generate APP_KEY if not set (BEFORE starting Docker!)
echo "🔑 Checking application key..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "   Generating application key..."
    RAND_KEY=$(openssl rand -base64 32)
    sed -i.bak "s|^APP_KEY=.*|APP_KEY=base64:$RAND_KEY|" .env && rm .env.bak
    echo "✅ Application key generated"
else
    echo "✅ Application key already exists"
fi

# Stop any existing containers
echo "🛑 Stopping existing containers (if any)..."
docker compose down 2>/dev/null || true

# Build containers
echo "🔨 Building Docker containers..."
docker compose build

# Start containers
echo "🚀 Starting Docker containers..."
docker compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Check MySQL health
echo "🔍 Checking MySQL connection..."
until docker compose exec mysql mysqladmin ping -h localhost --silent; do
    echo "   Waiting for MySQL..."
    sleep 2
done
echo "✅ MySQL is ready"

# Install PHP dependencies (if vendor doesn't exist)
echo "📦 Installing PHP dependencies..."
docker compose exec app composer install --no-interaction --prefer-dist

# Create storage directories if they don't exist
echo "📁 Creating required storage directories..."
docker compose exec app mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/testing storage/logs storage/app/public
docker compose exec app touch storage/logs/.gitignore

# Fix storage permissions BEFORE running migrations
echo "🔧 Fixing storage and cache permissions..."
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run migrations and seed database
echo "📊 Running database migrations and seeding..."
docker compose exec app php artisan migrate:fresh --seed --force

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "📋 Access Information:"
echo "   - Application: http://localhost:8000"
echo "   - phpMyAdmin: http://localhost:8080"
echo "     └─ User: laravel_user"
echo "     └─ Password: SecurePassword123!"
echo ""
echo "🔐 Default Login Credentials:"
echo "   - User: user@test.com / password"
echo "   - Admin: admin@test.com / password"
echo ""
echo "📝 Useful Commands:"
echo "   - View logs: docker compose logs -f app"
echo "   - Stop containers: docker compose down"
echo "   - Restart containers: docker compose restart"
echo "   - Access shell: docker compose exec app sh"
echo ""
