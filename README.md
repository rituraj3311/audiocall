# 🎧 Audio Call API

This is a Laravel-based backend API for implementing audio call functionality using Laravel. It supports the complete call lifecycle: start, respond, and end.

---

## 📦 Features

- Start audio call between users
- Accept or reject incoming calls
- End ongoing calls
- Clean RESTful API structure

---

## 🚀 Installation & Setup

### Prerequisites

- PHP 8.1+
- Composer
- MySQL
- Laravel 12

### Step-by-step Setup

```bash

# Install PHP dependencies
composer update

# Create .env from example and update your DB settings
cp .env.example .env

# Generate Laravel app key
php artisan key:generate

# Run database migrations
php artisan migrate

# Clear and cache config
php artisan config:clear
php artisan config:cache

# Optimize app
php artisan optimize

# Start the server
php artisan serve --port=9002
