# Subscription Module Documentation

## 1. Overview

The Subscription Module provides a comprehensive framework for managing various subscription plans for different user types within the application (Customers, Drivers, Merchants). It is designed to be flexible, supporting both credit-based and fixed-trip (BRINGFIX) models. The module includes a credit wallet system, dynamic rule-based credit deductions, and a full-featured admin panel for management.

## 2. Core Concepts

The module is built around two primary subscription models:

### a. Credit-Based Subscriptions

This is the standard model where users purchase a subscription package that grants them a specific number of credits. These credits are stored in a virtual wallet and are deducted when the user performs certain actions in the app.

- **User Types**: Supports `customer`, `driver`, and `merchant`.
- **Packages**: Admins can create packages with a specific price, validity period (in days), and a number of credits.
- **Shared Plans**: Plans can be configured for multiple members (e.g., BRINGFAM, BRINGCORPORATE) where a pool of credits is shared among a group of users.

### b. BRINGFIX (Fixed-Trip) Subscriptions

This is a specialized model for users who require a fixed number of trips on a pre-defined route and schedule. This system operates independently of the credit wallet.

- **Mechanism**: Users subscribe to a BRINGFIX package, define a fixed pickup and drop-off location, and set a recurring schedule for their trips.
- **Packages**: Admins define BRINGFIX package templates specifying the number of trips per month, maximum distance per trip, and price.

## 3. Database Schema

### Recent Schema Changes (2025-10-02)

**Credit Field Data Type Migration**: All credit-related fields have been migrated from `integer` to `decimal(10, 2)` to support fractional credit values. This enables more flexible pricing and deduction rules (e.g., 0.5 credits per kilometer, 2.75 credits per delivery).

**Migration File**: `Modules/Subscription/Database/Migrations/2025_10_02_172104_change_credit_fields_to_decimal.php`

**Affected Fields**:
- `subscription_packages.credits`: integer → decimal(10, 2)
- `subscriptions.total_credits`: integer → decimal(10, 2)
- `subscriptions.remaining_credits`: integer → decimal(10, 2)
- `credit_transactions.amount`: integer → decimal(10, 2)
- `credit_deduction_rules.credits_to_deduct`: decimal(8, 2) → decimal(10, 2)

**Model Casts Updated**:
- `SubscriptionPackage`: Added `'credits' => 'decimal:2'`
- `Subscription`: Added `'total_credits' => 'decimal:2'`, `'remaining_credits' => 'decimal:2'`
- `CreditTransaction`: Added `'amount' => 'decimal:2'`
- `CreditDeductionRule`: Added `'credits_to_deduct' => 'decimal:2'`, `'min_value' => 'decimal:2'`, `'max_value' => 'decimal:2'`
- `CreditWallet`: Updated from `'double'` to `'decimal:2'` for consistency

The module introduces and utilizes several key database tables:

| Table Name                      | Description                                                                                             |
| ------------------------------- | ------------------------------------------------------------------------------------------------------- |
| `subscription_packages`         | Stores the master catalog of all available credit-based subscription plans.                             |
| `subscriptions`                 | Records active user subscriptions, linking a user to a `subscription_packages`. Tracks total and remaining credits per subscription. |
| `credit_wallets`                | **NEW**: Holds the aggregated current credit balance for each user using polymorphic relationships (`user_id`, `user_type`). |
| `credit_transactions`           | Logs all credit movements (purchases, usage, deductions, refunds) for auditing purposes. Uses polymorphic relationships for both user and reference entities. |
| `credit_deduction_rules`        | A powerful rule engine to define how many credits are deducted for specific actions.                    |
| `bringfix_packages`             | Stores the templates for BRINGFIX plans.                                                                |
| `user_bringfix_subscriptions`   | Stores a user's specific BRINGFIX subscription, including their custom route and remaining trips.         |
| `user_bringfix_schedules`       | Stores the recurring schedule (days and times) for a user's BRINGFIX subscription.                        |

## 4. Services and Business Logic

### a. `App\Services\WalletService`

This is the primary service for managing the credit wallet. It handles all credit operations atomically to ensure data integrity. **This service was introduced in commit 638d5a5 and replaces the older `CreditService`**.

**Key Features:**
- **Polymorphic Support**: Works with `User`, `DeliveryMan`, and `Vendor` models
- **Atomic Transactions**: All credit operations use database transactions for consistency
- **Automatic Wallet Creation**: Wallets are created lazily via the `HasCreditWallet` trait

**Methods:**

- `addCredits(User|DeliveryMan|Vendor $user, float $amount, string $transaction_type, $reference, ?string $details)`:
  - Adds credits to a user's wallet (increments `credit_wallets.balance`)
  - Creates a `CreditTransaction` record with positive amount
  - Called after successful subscription purchase
  - Validates amount is positive

- `deductCredits(User|DeliveryMan|Vendor $user, float $amount, string $transaction_type, $reference, ?string $details)`:
  - Deducts credits from a user's wallet (decrements `credit_wallets.balance`)
  - Creates a `CreditTransaction` record with negative amount
  - **FIFO Deduction Logic**: Deducts from subscriptions ordered by expiry date (oldest first)
  - Updates `remaining_credits` in individual `subscriptions` records
  - Throws exception if insufficient balance
  - Used for parcel delivery, order placement, and expired credit removal

**Transaction Types:**
- `purchase`: Credits added from subscription purchase
- `usage`: Credits consumed for services (orders, rides)
- `deduction`: Credits removed (e.g., expired subscriptions, manual adjustments)
- `refund`: Credits returned to user

### b. `Modules\Subscription\Traits\HasCreditWallet`

A trait applied to `User`, `DeliveryMan`, and `Vendor` models to provide credit wallet functionality.

**Methods:**
- `creditWallet()`: Polymorphic relationship to `CreditWallet` model
- `getCreditWalletAndEnsuredSaved()`: Lazily creates and returns the wallet if it doesn't exist
- `getCreditBalanceAttribute()`: Accessor for `$user->credit_balance`
- `creditTransactions()`: Polymorphic relationship to all user's `CreditTransaction` records

### c. `Modules\Subscription\Console\MarkExpiredSubscriptions` (**NEW**)

A scheduled command to handle subscription expiry automatically.

**Functionality:**
- Command: `subscription:mark-expired`
- Finds all active subscriptions (`status = 1`, `is_canceled = 0`) past their `expiry_date`
- Sets subscription `status` to `0` (inactive)
- Deducts remaining credits from user's wallet using `WalletService::deductCredits()`
- Creates a `deduction` transaction with details "Expired credits"
- Should be scheduled to run daily via Laravel's task scheduler

### d. `Modules\Subscription\Services\CreditService` (**DEPRECATED**)

This service was part of the older implementation that directly decremented credits from the `subscriptions` table. **It has been replaced by the more robust `WalletService` and should no longer be used**.

## 5. Admin Dashboard

The admin panel provides comprehensive tools to manage the entire subscription system. All subscription-related routes are prefixed with `/admin/users/subscription/`.

### a. Package Management (`packages`)

- **URL**: `/admin/users/subscription/packages`
- **Controller**: `Modules\Subscription\Http\Controllers\Admin\PackageController`
- **Description**: Allows admins to Create, Read, Update, and Delete (CRUD) credit-based subscription packages. Admins can define the package name, target user type, price, validity, and the number of credits.

### b. Credit Deduction Rules (`credit-rules`)

- **URL**: `/admin/users/subscription/credit-rules`
- **Controller**: `Modules\Subscription\Http\Controllers\Admin\CreditDeductionRuleController`
- **Description**: A flexible interface to define the rules for credit consumption. For example, an admin can set a rule to deduct 0.5 credits for a ride-hailing trip or deduct a specific amount based on an order's price range.

### c. View Subscriptions (`list`)

- **URL**: `/admin/users/subscription/list`
- **Controller**: `Modules\Subscription\Http\Controllers\Admin\SubscriptionController`
- **Description**: A read-only list of all active and expired user subscriptions.

### d. BRINGFIX Package Management (`bringfix/packages`)

- **URL**: `/admin/users/subscription/bringfix/packages`
- **Controller**: `Modules\Subscription\Http\Controllers\Admin\BringfixPackageController`
- **Description**: Provides full CRUD functionality for BRINGFIX package templates.

### e. View BRINGFIX Subscriptions (`bringfix/list`)

- **URL**: `/admin/users/subscription/bringfix/list`
- **Controller**: `Modules\Subscription\Http\Controllers\Admin\UserBringfixSubscriptionController`
- **Description**: A list of all users subscribed to BRINGFIX plans, with the ability to view details of their custom routes and schedules.

### f. Credit Transactions (`transactions`)

- **URL**: `/admin/users/subscription/transactions`
- **Controller**: `Modules\Subscription\Http\Controllers\Admin\CreditTransactionController`
- **Description**: A log of all credit transactions across the system for auditing and tracking.

## 6. API Endpoints

The module exposes a set of API endpoints for client applications (e.g., mobile apps). **Routes were reorganized in commit e56e0a2** to provide user-type specific endpoints with dedicated authentication guards.

### a. Authentication

- Endpoints are user-type specific and use separate authentication guards:
  - **Customer**: `auth:api` (uses Laravel Passport OAuth2)
  - **Driver**: `auth:dm-api` (uses simple bearer token from `auth_token` field in `delivery_men` table)
  - **Merchant**: `auth:vendor-api` (uses simple bearer token from `auth_token` field in `vendors` table)

### b. Endpoints

#### Subscription Packages

**Customer Endpoint:**
- **Endpoint**: `GET /api/v1/subscription/v2/packages`
- **Middleware**: `auth:api`

**Driver Endpoint:**
- **Endpoint**: `GET /api/v1/delivery-man/subscription/v2/packages`
- **Middleware**: `auth:dm-api`

**Merchant Endpoint:**
- **Endpoint**: `GET /api/v1/vendor/subscription/v2/packages`
- **Middleware**: `auth:vendor-api`

- **Controller**: `Modules\Subscription\Http\Controllers\Api\V1\SubscriptionController@index`
- **Description**: Fetches a list of available subscription packages for the authenticated user's type. Automatically determines user type from the authenticated model class.
- **Response**: A JSON array of `SubscriptionPackage` objects filtered by user type.

#### Purchase Subscription

**Customer Endpoint:**
- **Endpoint**: `POST /api/v1/subscription/v2/packages`
- **Middleware**: `auth:api`

**Driver Endpoint:**
- **Endpoint**: `POST /api/v1/delivery-man/subscription/v2/packages`
- **Middleware**: `auth:dm-api`

**Merchant Endpoint:**
- **Endpoint**: `POST /api/v1/vendor/subscription/v2/packages`
- **Middleware**: `auth:vendor-api`

- **Controller**: `Modules\Subscription\Http\Controllers\Api\V1\SubscriptionController@store`
- **Description**: Allows a user to purchase a subscription. Creates subscription record and adds credits to wallet via `WalletService`.
- **Request Body**:
  ```json
  {
      "package_id": 123
  }
  ```
- **Note**: This endpoint has a `TODO` for payment gateway integration. Currently, it assumes payment is successful and directly creates the subscription.

#### List User Subscriptions

**Customer Endpoint:**
- **Endpoint**: `GET /api/v1/subscription/v2/list`
- **Middleware**: `auth:api`

**Driver Endpoint:**
- **Endpoint**: `GET /api/v1/delivery-man/subscription/v2/list`
- **Middleware**: `auth:dm-api`

**Merchant Endpoint:**
- **Endpoint**: `GET /api/v1/vendor/subscription/v2/list`
- **Middleware**: `auth:vendor-api`

- **Controller**: `Modules\Subscription\Http\Controllers\Api\V1\SubscriptionController@list`
- **Description**: Fetches all subscriptions for the authenticated user from the `subscriptions` table, ordered by most recent first. Includes related package details via eager loading.
- **Response**: A JSON array of `Subscription` objects with nested `package` relationship.

#### Get Latest Subscription

**Customer Endpoint:**
- **Endpoint**: `GET /api/v1/subscription/v2/latest`
- **Middleware**: `auth:api`

**Driver Endpoint:**
- **Endpoint**: `GET /api/v1/delivery-man/subscription/v2/latest`
- **Middleware**: `auth:dm-api`

**Merchant Endpoint:**
- **Endpoint**: `GET /api/v1/vendor/subscription/v2/latest`
- **Middleware**: `auth:vendor-api`

- **Controller**: `Modules\Subscription\Http\Controllers\Api\V1\SubscriptionController@latest`
- **Description**: Fetches the single most recent subscription for the authenticated user. Includes related package details.
- **Response**: A JSON object of the latest `Subscription` with nested `package` relationship, or 404 error if no subscription exists.

#### Credit Wallet

**Customer Endpoint:**
- **Endpoint**: `GET /api/v1/credit-wallet`
- **Middleware**: `auth:api`

**Driver Endpoint:**
- **Endpoint**: `GET /api/v1/delivery-man/credit-wallet`
- **Middleware**: `auth:dm-api`

**Merchant Endpoint:**
- **Endpoint**: `GET /api/v1/vendor/credit-wallet`
- **Middleware**: `auth:vendor-api`

- **Controller**: `Modules\Subscription\Http\Controllers\Api\V1\CreditWalletController@get_wallet`
- **Description**: Retrieves the authenticated user's current credit wallet object from `credit_wallets` table.
- **Response**: Returns the complete `CreditWallet` model (see API documentation for full response schema)

#### Wallet Transactions

**Customer Endpoint:**
- **Endpoint**: `GET /api/v1/credit-wallet/transactions`
- **Middleware**: `auth:api`

**Driver Endpoint:**
- **Endpoint**: `GET /api/v1/delivery-man/credit-wallet/transactions`
- **Middleware**: `auth:dm-api`

**Merchant Endpoint:**
- **Endpoint**: `GET /api/v1/vendor/credit-wallet/transactions`
- **Middleware**: `auth:vendor-api`

- **Controller**: `Modules\Subscription\Http\Controllers\Api\V1\CreditWalletController@get_transactions`
- **Description**: Fetches a paginated list of the user's credit transactions using polymorphic relationships.
- **Query Parameters**: `limit` (optional, default 10), `page` (optional).
- **Response**: A paginated JSON response of `CreditTransaction` objects with polymorphic `user` and `reference` relationships.

## 7. Credit Deduction for Orders (NEW - Commit 638d5a5)

The subscription module now integrates with the order placement flow to automatically deduct credits from users' wallets when placing orders.

### Implementation Details

**Location**: `app\Traits\PlaceNewOrder.php` (integrated into order controller)

**How It Works:**
1. When a customer places an order (parcel delivery), the system checks if they have sufficient credits
2. Credits are deducted using `WalletService::deductCredits()`
3. The deduction follows FIFO logic based on subscription expiry dates
4. A `usage` transaction is created with reference to the order
5. If insufficient credits, an exception is thrown and order placement fails

**Credit Deduction Rules:**
- Credits are deducted based on rules defined in the `credit_deduction_rules` table
- Rules can be configured by admin for different:
  - **Condition Types**: `ride_hailing`, `ride_share`, `distance_range`, `price_range`
  - **User Types**: `customer`, `driver`, `merchant`
  - **Modules**: Different modules can have different deduction rates
  - **Value Ranges**: Different credit amounts for different distance/price ranges

**Example Flow:**
1. Customer with 50 credits in wallet places a parcel delivery order
2. System finds applicable credit deduction rule (e.g., 5 credits per delivery)
3. `WalletService::deductCredits($user, 5, 'usage', $order, 'Parcel delivery order #123')` is called
4. Credits are deducted from the oldest expiring subscription first
5. User's wallet balance is updated to 45 credits
6. Transaction is logged in `credit_transactions` table
7. Order is placed successfully

**Error Handling:**
- If user has insufficient credits, `WalletService::deductCredits()` throws an exception
- Order placement is rolled back
- User receives error message about insufficient credits

## 8. Scheduled Tasks

### Subscription Expiry Command

The module includes a console command that should be scheduled to run daily to handle expired subscriptions.

**Command**: `php artisan subscription:mark-expired`

**Add to** `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('subscription:mark-expired')->daily();
}
```

**What it does:**
- Finds all active subscriptions past their expiry date
- Marks them as inactive
- Deducts any remaining credits from the user's wallet
- Creates deduction transactions for audit trail
