# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a multi-service Laravel 10 application built for a delivery/rental platform with modular architecture. The application supports multiple business models including food delivery, e-commerce, pharmacy, rental vehicles, and ride-sharing services. It includes separate admin, vendor, and customer interfaces with comprehensive payment gateway integrations.

**Core Technologies:**
- Laravel 10 (PHP 8.2+)
- Laravel Passport for API authentication
- Laravel Modules (nwidart/laravel-modules) for modular architecture
- Laravel Mix for frontend asset compilation
- Firebase for push notifications and real-time features
- Multiple payment gateways (Stripe, PayPal, Razorpay, etc.)

## Development Commands

### Basic Laravel Commands
```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database operations
php artisan migrate
php artisan db:seed
php artisan migrate:fresh --seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache

# Run development server
php artisan serve

# Queue management
php artisan queue:work
php artisan queue:restart

# WebSocket server (for real-time features)
php artisan websockets:serve
```

### Frontend Assets
```bash
# Development
npm run dev
npm run watch

# Production
npm run production
```

### Testing
```bash
# Run all tests
php artisan test
vendor/bin/phpunit

# Run specific test suite
vendor/bin/phpunit --testsuite=Feature
vendor/bin/phpunit --testsuite=Unit

# Run specific test file
vendor/bin/phpunit tests/Feature/ExampleTest.php
```

## Architecture Overview

### Modular Structure

The application uses **Laravel Modules** for organizing features into self-contained modules located in the `Modules/` directory:

- **Rental Module** (`Modules/Rental/`): Vehicle rental management with providers, drivers, vehicles, trips, and bookings
- **Subscription Module** (`Modules/Subscription/`): Subscription packages, billing, credit wallet system, and refund management
- **TaxModule** (`Modules/TaxModule/`): Tax and VAT configuration and calculations

Each module contains its own:
- Controllers (Admin, Vendor/Provider, API endpoints)
- Models/Entities
- Routes
- Views
- Migrations
- Service Providers

Module status is tracked in `modules_statuses.json`.

### Main Application Structure

**Controllers** (`app/Http/Controllers/`):
- **Admin Controllers** (`Admin/*`): Full administrative control (orders, vendors, customers, modules, reports, settings)
- **Vendor Controllers** (`Vendor/*`): Store/restaurant/provider management interfaces
- **Payment Controllers**: Various payment gateway integrations (Stripe, PayPal, Razorpay, Flutterwave, etc.)
- **Base Controllers**: `BaseController.php` and `Controller.php` provide common functionality

**Business Logic** (`app/CentralLogics/`):
- Global helper functions for orders, stores, items, customers, coupons, campaigns, banners, notifications
- These files are autoloaded via `composer.json` and contain reusable business logic across the application
- Examples: `order.php`, `store.php`, `item.php`, `customer.php`, `helpers.php`

**Traits** (`app/Traits/`):
- `PaymentGatewayTrait.php`: Payment processing logic
- `NotificationTrait.php`: Notification sending mechanisms
- `FileManagerTrait.php`: File upload and management
- `PlaceNewOrder.php`: Order placement logic
- `SmsGateway.php`: SMS sending functionality

**Services** (`app/Services/`):
- Service layer for business logic (e.g., `WalletService`, `CouponService`, `NotificationService`, `ModuleService`)
- Services handle complex operations and coordinate between repositories and controllers

**Repositories** (`app/Repositories/`):
- Data access layer following repository pattern
- Interface binding configured in `app/Providers/InterfaceServiceProvider.php`
- Each repository has a corresponding interface in `app/Contracts/Repositories/`
- Examples: `ZoneRepository`, `StoreRepository`, `OrderRepository`, `CouponRepository`

**Models** (`app/Models/`):
- Core entities: `User`, `Order`, `Store`, `Item`, `Zone`, `DeliveryMan`, `Vendor`
- Subscription models: `SubscriptionPackage`, `StoreSubscription`, `SubscriptionTransaction`
- Payment models: `WalletTransaction`, `WalletPayment`, `OrderTransaction`
- Rental-specific models are in `Modules/Rental/Entities/`

**Observers** (`app/Observers/`):
- Model lifecycle hooks (e.g., `OrderObserver`, `UserObserver`, `ModuleObserver`, `BusinessSettingObserver`)
- Registered in `app/Providers/EventServiceProvider.php`

**Scopes** (`app/Scopes/`):
- Global query scopes for filtering data (e.g., `ZoneScope`, `StoreScope`)

### Multi-Business Model System

The platform supports multiple business models configured per store/vendor:
- **Commission-based**: Platform takes a commission on orders
- **Subscription-based**: Vendors pay recurring subscription fees
- **Credit-based**: Vendors purchase credits for order processing

Business model is stored in `stores.store_business_model` column.

### Zone-Based Architecture

The application uses geographical zones (`Zone` model) to organize:
- Available stores/vendors per zone
- Delivery coverage areas
- Module availability per zone (`ModuleZone` model)
- Pricing and delivery fee calculations

### Helper Functions

Two main helper files are autoloaded:
- `app/helpers.php`: Application-wide helper functions (e.g., `translate()`, payment processing helpers)
- `app/CentralLogics/helpers.php`: `Helpers` class with static methods for common operations

### Multi-Language Support

- Language files in `resources/lang/{locale}/messages.php`
- `translate()` function auto-creates missing translation keys
- `Translation` model for translatable content in the database

### API Structure

API routes are organized by version and user type:
- User/Customer APIs: `api/v1/*`
- Vendor/Provider APIs: `api/v1/vendor/*` or `api/v1/provider/*`
- Delivery Man APIs: `api/v1/delivery-man/*`

Module-specific APIs are in their respective modules (e.g., `Modules/Rental/Routes/api/v1/*`).

## Key Configuration Files

- `config/module.php` & `config/modules.php`: Module system configuration
- `config/default_pagination.php`: Pagination defaults
- `config/system-addons.php`: Addon/plugin system
- Payment gateway configs: `config/paypal.php`, `config/razor.php`, `config/stripe.php`, etc.

## Database Migrations

Migrations follow this naming pattern: `YYYY_MM_DD_HHMMSS_description.php`

Recent important migrations include:
- Credit wallet system (`2025_09_15_132204_create_credit_wallets_table.php`)
- Order credit fields (`2025_10_04_085554_add_credit_fields_to_orders_table.php`)
- Subscription billing history (`2024_05_22_115717_create_subscription_billing_and_refund_histories_table.php`)
- Module-specific features (nutrition, allergies, brands, flash sales)

## Payment Gateway Integration

The application supports 15+ payment gateways:
- Stripe, PayPal, Razorpay, Flutterwave, SSLCommerz
- Paytm, Paytabs, Paystack, MercadoPago, Bkash
- PhonePe, Xendit, Iyzico, LiqPay, SenangPay

Payment logic is centralized in `app/Traits/PaymentGatewayTrait.php` and individual controller classes.

## Notification System

Multi-channel notification support:
- Push notifications (Firebase Cloud Messaging)
- Email notifications (various mail drivers)
- SMS notifications (Twilio, custom SMS gateways in `app/CentralLogics/sms_module.php`)
- In-app notifications (`UserNotification` model)

Notification settings managed via `NotificationSetting` and `StoreNotificationSetting` models.

## Important Notes

- **Repository Pattern**: When querying data, prefer using repository methods over direct model queries for consistency
- **Zone Scoping**: Be aware of global zone scopes when working with models like `Store` and `Item`
- **Module Status**: Check `modules_statuses.json` to see which modules are enabled
- **Business Settings**: Many features are controlled by database settings in `business_settings` table
- **Subscription Credits**: Credit-based subscriptions track usage in `credit_transactions` table
- **Multi-tenancy**: The system supports multiple vendors/stores with isolated data per zone
