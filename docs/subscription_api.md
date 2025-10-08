# API Documentation: Subscription System

This documentation outlines the endpoints for managing subscription packages, credit wallets, and transaction history across all user types.

**Base URL**: `https://your-domain.com/api/v1/`

## Recent Changes

### Credit Field Data Types (Updated: 2025-10-02)

All credit-related fields have been migrated from `integer` to `decimal(10, 2)` to support fractional credit values (e.g., 0.5 credits, 2.75 credits). This change affects:

- **subscription_packages**: `credits` field
- **subscriptions**: `total_credits` and `remaining_credits` fields
- **credit_transactions**: `amount` field
- **credit_deduction_rules**: `credits_to_deduct` field (expanded from decimal(8,2) to decimal(10,2))
- **credit_wallets**: `balance` field (already decimal(10,2))

All API responses now return credit values as decimal numbers with up to 2 decimal places.

## Authentication System

The subscription system uses **user-type segregated routes** with dedicated authentication guards. Each user type has specific endpoints with appropriate middleware:

| User Type | Model | Token Type | Token Field | Auth Middleware |
|-----------|-------|------------|-------------|-----------------|
| **Customer** | `App\Models\User` | Passport OAuth2 | Generated via Passport | `auth:api` |
| **Driver** | `App\Models\DeliveryMan` | Simple Bearer | `auth_token` field | `auth:dm-api` |
| **Merchant** | `App\Models\Vendor` | Simple Bearer | `auth_token` field | `auth:vendor-api` |

### How It Works

1. **Dedicated Endpoints**: Each user type has specific route prefixes for clear separation
2. **Guard-Specific Authentication**: Each endpoint uses the appropriate authentication guard
3. **Type-Safe Responses**: Returns data specific to the authenticated user type
4. **Predictable URLs**: Easy to understand and implement on client side

### Authentication Headers

All requests must include an `Authorization` header with a bearer token:

```json
{
  "Authorization": "Bearer YOUR_API_TOKEN",
  "Accept": "application/json"
}
```

---

# Credit Wallet Management

## 1. Get User Wallet Balance

Retrieves the authenticated user's current credit wallet balance. Works for all user types with their specific endpoints. The balance is stored in a dedicated `credit_wallets` table and represents the aggregated total from all active subscriptions.

### Endpoints by User Type

-   **Customer**: `GET /credit-wallet`
-   **Driver**: `GET /delivery-man/credit-wallet`
-   **Merchant**: `GET /vendor/credit-wallet`

#### Success Response (`200 OK`)

Returns a JSON object with the user's credit wallet details including ID, balance, and timestamps.

**Example Body**:
```json
{
    "id": 12,
    "user_id": 45,
    "user_type": "App\\Models\\User",
    "balance": 150.5,
    "created_at": "2023-09-15T10:00:00.000000Z",
    "updated_at": "2023-09-16T14:30:00.000000Z"
}
```

| Field           | Type   | Description                                           |
|-----------------|--------|-------------------------------------------------------|
| `id`            | integer | The unique ID of the credit wallet record. |
| `user_id`       | integer | The ID of the user who owns this wallet. |
| `user_type`     | string | The fully qualified class name of the user model (polymorphic). |
| `balance`       | decimal | The total available credit balance (decimal with 2 decimal places). |
| `created_at`    | string | The ISO-8601 timestamp when the wallet was created. |
| `updated_at`    | string | The ISO-8601 timestamp when the wallet was last updated. |

#### Error Response (`401 Unauthorized`)
Returned if the request lacks a valid API token.

**Example Body**:
```json
{
    "message": "Unauthenticated."
}
```

---

## 2. Get Wallet Transactions

Fetches a paginated list of the authenticated user's credit transactions, sorted by the most recent. Works for all user types with their specific endpoints. Uses polymorphic relationships to retrieve transactions across different user types.

### Endpoints by User Type

-   **Customer**: `GET /credit-wallet/transactions`
-   **Driver**: `GET /delivery-man/credit-wallet/transactions`
-   **Merchant**: `GET /vendor/credit-wallet/transactions`

#### Query Parameters

| Parameter | Type    | Optional | Default | Description                               |
|-----------|---------|----------|---------|-------------------------------------------|
| `limit`   | integer | Yes      | 10      | The number of transactions to return per page. |
| `page`    | integer | Yes      | 1       | The page number to retrieve.              |


#### Success Response (`200 OK`)

Returns a standard Laravel paginated JSON response containing the transaction data. Transactions use polymorphic relationships for both the user and reference (e.g., subscription, order).

**Example Body**:
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 101,
            "user_id": 12,
            "user_type": "App\\Models\\User",
            "amount": -5.50,
            "transaction_type": "usage",
            "reference_id": 456,
            "reference_type": "App\\Models\\Order",
            "details": "Credit deduction for order #456",
            "created_at": "2023-09-16T12:00:00.000000Z",
            "updated_at": "2023-09-16T12:00:00.000000Z"
        },
        {
            "id": 100,
            "user_id": 12,
            "user_type": "App\\Models\\User",
            "amount": 100.00,
            "transaction_type": "purchase",
            "reference_id": 78,
            "reference_type": "Modules\\Subscription\\Entities\\Subscription",
            "details": "Purchased Basic Customer Credits plan",
            "created_at": "2023-09-15T10:00:00.000000Z",
            "updated_at": "2023-09-15T10:00:00.000000Z"
        }
    ],
    "first_page_url": "https://your-domain.com/api/v1/credit-wallet/transactions?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "https://your-domain.com/api/v1/credit-wallet/transactions?page=5",
    "next_page_url": "https://your-domain.com/api/v1/credit-wallet/transactions?page=2",
    "path": "https://your-domain.com/api/v1/credit-wallet/transactions",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 50
}
```

#### Transaction Fields

| Field              | Type    | Description                                           |
|--------------------|---------|-------------------------------------------------------|
| `id`               | integer | Unique transaction ID. |
| `user_id`          | integer | The ID of the user (polymorphic). |
| `user_type`        | string  | The fully qualified class name of the user model. |
| `amount`           | decimal | Transaction amount (positive for credits added, negative for deductions). Decimal with 2 decimal places. |
| `transaction_type` | string  | Type of transaction: `purchase`, `usage`, `deduction`, `refund`. |
| `reference_id`     | integer | The ID of the related entity (polymorphic - can be Subscription, Order, etc.). |
| `reference_type`   | string  | The fully qualified class name of the reference entity. |
| `details`          | string  | Human-readable description of the transaction. |
| `created_at`       | string  | ISO-8601 timestamp when the transaction was created. |
| `updated_at`       | string  | ISO-8601 timestamp when the transaction was last updated. |

#### Error Response (`401 Unauthorized`)
Returned if the request lacks a valid API token.

**Example Body**:
```json
{
    "message": "Unauthenticated."
}
```

---

# Subscription Package Management

## 1. Get Subscription Packages

Fetches a list of available subscription packages for the authenticated user's type. Each user type has a dedicated endpoint that returns packages specific to their role.

### Endpoints by User Type

-   **Customer**: `GET /subscription/v2/packages`
-   **Driver**: `GET /delivery-man/subscription/v2/packages`
-   **Merchant**: `GET /vendor/subscription/v2/packages`

#### Success Response (`200 OK`)

Returns a JSON array of subscription package objects.

**Example Response for Customer:**
```json
[
    {
        "id": 1,
        "package_name": "Basic Customer Credits",
        "package_details": "A starter pack of credits for customers.",
        "price": 25.00,
        "credits": 50,
        "validity": 30,
        "user_type": "customer",
        "status": 1,
        "created_at": "2023-09-15T10:00:00.000000Z",
        "updated_at": "2023-09-15T10:00:00.000000Z"
    }
]
```

**Example Response for Driver:**
```json
[
    {
        "id": 2,
        "package_name": "Driver Credits Pack",
        "package_details": "Credits for delivery drivers.",
        "price": 30.00,
        "credits": 75,
        "validity": 30,
        "user_type": "driver",
        "status": 1,
        "created_at": "2023-09-15T10:00:00.000000Z",
        "updated_at": "2023-09-15T10:00:00.000000Z"
    }
]
```

**Example Response for Merchant:**
```json
[
    {
        "id": 3,
        "package_name": "Merchant Business Pack",
        "package_details": "Credits for merchant operations.",
        "price": 100.00,
        "credits": 200,
        "validity": 30,
        "user_type": "merchant",
        "status": 1,
        "created_at": "2023-09-15T10:00:00.000000Z",
        "updated_at": "2023-09-15T10:00:00.000000Z"
    }
]
```

---

## 2. Purchase a Subscription Package

Subscribes the authenticated user to a new package. This endpoint creates a new subscription record and adds the corresponding credits to the user's credit wallet via the `WalletService`. Each user type has a dedicated endpoint.

**Important**: This endpoint currently assumes payment is successful (payment gateway integration is pending). In production, implement proper payment processing before creating the subscription.

### Endpoints by User Type

-   **Customer**: `POST /subscription/v2/packages`
-   **Driver**: `POST /delivery-man/subscription/v2/packages`
-   **Merchant**: `POST /vendor/subscription/v2/packages`

#### Request Body

| Field        | Type    | Required | Description                        |
|--------------|---------|----------|------------------------------------|
| `package_id` | integer | Yes      | The ID of the package to purchase. |

**Example Body**:
```json
{
    "package_id": 1
}
```

#### Success Response (`201 Created`)

Returns a success message and the newly created subscription object. The `subscriber_type` will vary based on the authenticated user type. Credits are automatically added to the user's wallet and a `purchase` transaction is created in the `credit_transactions` table.

**Example Response for Customer:**
```json
{
    "message": "Subscription purchased successfully!",
    "subscription": {
        "subscriber_id": 12,
        "subscriber_type": "App\\Models\\User",
        "package_id": 1,
        "expiry_date": "2023-10-16T15:00:00.000000Z",
        "total_credits": 50,
        "remaining_credits": 50,
        "status": true,
        "id": 78
    }
}
```

**Example Response for Driver:**
```json
{
    "message": "Subscription purchased successfully!",
    "subscription": {
        "subscriber_id": 25,
        "subscriber_type": "App\\Models\\DeliveryMan",
        "package_id": 2,
        "expiry_date": "2023-10-16T15:00:00.000000Z",
        "total_credits": 75,
        "remaining_credits": 75,
        "status": true,
        "id": 79
    }
}
```

**Example Response for Merchant:**
```json
{
    "message": "Subscription purchased successfully!",
    "subscription": {
        "subscriber_id": 8,
        "subscriber_type": "App\\Models\\Vendor",
        "package_id": 3,
        "expiry_date": "2023-10-16T15:00:00.000000Z",
        "total_credits": 200,
        "remaining_credits": 200,
        "status": true,
        "id": 80
    }
}
```

#### Error Response (`422 Unprocessable Entity`)
Returned if the `package_id` is missing or invalid.

**Example Body**:
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "package_id": [
            "The selected package id is invalid."
        ]
    }
}
```

---

## 3. Get User Subscription List

Retrieves all subscriptions for the authenticated user from the `subscriptions` table, ordered by most recent first. Returns complete subscription records including package details via eager loading. Works for all user types with their specific endpoints.

### Endpoints by User Type

-   **Customer**: `GET /subscription/v2/list`
-   **Driver**: `GET /delivery-man/subscription/v2/list`
-   **Merchant**: `GET /vendor/subscription/v2/list`

#### Success Response (`200 OK`)

Returns a JSON array of subscription objects with nested package information.

**Example Response for Customer:**
```json
[
    {
        "id": 78,
        "package_id": 1,
        "subscriber_id": 12,
        "subscriber_type": "App\\Models\\User",
        "expiry_date": "2023-10-16T15:00:00.000000Z",
        "total_credits": 50,
        "remaining_credits": 25,
        "status": true,
        "is_trial": false,
        "total_package_renewed": 0,
        "renewed_at": null,
        "is_canceled": false,
        "canceled_by": "none",
        "created_at": "2023-09-16T15:00:00.000000Z",
        "updated_at": "2023-09-20T10:30:00.000000Z",
        "package": {
            "id": 1,
            "package_name": "Basic Customer Credits",
            "package_details": "A starter pack of credits for customers.",
            "price": 25.00,
            "credits": 50,
            "validity": 30,
            "user_type": "customer",
            "status": 1,
            "created_at": "2023-09-15T10:00:00.000000Z",
            "updated_at": "2023-09-15T10:00:00.000000Z"
        }
    },
    {
        "id": 65,
        "package_id": 1,
        "subscriber_id": 12,
        "subscriber_type": "App\\Models\\User",
        "expiry_date": "2023-09-15T15:00:00.000000Z",
        "total_credits": 50,
        "remaining_credits": 0,
        "status": false,
        "is_trial": false,
        "total_package_renewed": 0,
        "renewed_at": null,
        "is_canceled": false,
        "canceled_by": "none",
        "created_at": "2023-08-16T15:00:00.000000Z",
        "updated_at": "2023-09-16T00:00:00.000000Z",
        "package": {
            "id": 1,
            "package_name": "Basic Customer Credits",
            "package_details": "A starter pack of credits for customers.",
            "price": 25.00,
            "credits": 50,
            "validity": 30,
            "user_type": "customer",
            "status": 1,
            "created_at": "2023-09-15T10:00:00.000000Z",
            "updated_at": "2023-09-15T10:00:00.000000Z"
        }
    }
]
```

#### Subscription Fields

| Field                  | Type    | Description                                           |
|------------------------|---------|-------------------------------------------------------|
| `id`                   | integer | Unique subscription ID. |
| `package_id`           | integer | The ID of the purchased package. |
| `subscriber_id`        | integer | The ID of the user (polymorphic). |
| `subscriber_type`      | string  | The fully qualified class name of the user model. |
| `expiry_date`          | string  | ISO-8601 timestamp when the subscription expires. |
| `total_credits`        | decimal | The original number of credits included in the subscription (decimal with 2 decimal places). |
| `remaining_credits`    | decimal | The number of credits still available (decimal with 2 decimal places). |
| `status`               | boolean | Whether the subscription is active (`true`) or expired (`false`). |
| `is_trial`             | boolean | Whether this is a trial subscription. |
| `total_package_renewed`| integer | Number of times the package has been renewed. |
| `renewed_at`           | string\|null | ISO-8601 timestamp when the subscription was last renewed. |
| `is_canceled`          | boolean | Whether the subscription has been canceled. |
| `canceled_by`          | string  | Who canceled the subscription: `none`, `admin`, or `user`. |
| `created_at`           | string  | ISO-8601 timestamp when the subscription was created. |
| `updated_at`           | string  | ISO-8601 timestamp when the subscription was last updated. |
| `package`              | object  | Nested package object with full package details. |

#### Error Response (`401 Unauthorized`)
Returned if the request lacks a valid API token.

**Example Body**:
```json
{
    "message": "Unauthenticated."
}
```

---

## 4. Get Latest Subscription

Retrieves the single most recent subscription for the authenticated user. This is useful for checking the user's current active subscription or their last purchased plan. Works for all user types with their specific endpoints.

### Endpoints by User Type

-   **Customer**: `GET /subscription/v2/latest`
-   **Driver**: `GET /delivery-man/subscription/v2/latest`
-   **Merchant**: `GET /vendor/subscription/v2/latest`

#### Success Response (`200 OK`)

Returns a single subscription object with nested package information.

**Example Response for Customer:**
```json
{
    "id": 78,
    "package_id": 1,
    "subscriber_id": 12,
    "subscriber_type": "App\\Models\\User",
    "expiry_date": "2023-10-16T15:00:00.000000Z",
    "total_credits": 50,
    "remaining_credits": 25,
    "status": true,
    "is_trial": false,
    "total_package_renewed": 0,
    "renewed_at": null,
    "is_canceled": false,
    "canceled_by": "none",
    "created_at": "2023-09-16T15:00:00.000000Z",
    "updated_at": "2023-09-20T10:30:00.000000Z",
    "package": {
        "id": 1,
        "package_name": "Basic Customer Credits",
        "package_details": "A starter pack of credits for customers.",
        "price": 25.00,
        "credits": 50,
        "validity": 30,
        "user_type": "customer",
        "status": 1,
        "created_at": "2023-09-15T10:00:00.000000Z",
        "updated_at": "2023-09-15T10:00:00.000000Z"
    }
}
```

**Example Response for Driver:**
```json
{
    "id": 79,
    "package_id": 2,
    "subscriber_id": 25,
    "subscriber_type": "App\\Models\\DeliveryMan",
    "expiry_date": "2023-10-16T15:00:00.000000Z",
    "total_credits": 75,
    "remaining_credits": 60,
    "status": true,
    "is_trial": false,
    "total_package_renewed": 1,
    "renewed_at": "2023-09-01T10:00:00.000000Z",
    "is_canceled": false,
    "canceled_by": "none",
    "created_at": "2023-09-16T15:00:00.000000Z",
    "updated_at": "2023-09-18T08:15:00.000000Z",
    "package": {
        "id": 2,
        "package_name": "Driver Credits Pack",
        "package_details": "Credits for delivery drivers.",
        "price": 30.00,
        "credits": 75,
        "validity": 30,
        "user_type": "driver",
        "status": 1,
        "created_at": "2023-09-15T10:00:00.000000Z",
        "updated_at": "2023-09-15T10:00:00.000000Z"
    }
}
```

**Example Response for Merchant:**
```json
{
    "id": 80,
    "package_id": 3,
    "subscriber_id": 8,
    "subscriber_type": "App\\Models\\Vendor",
    "expiry_date": "2023-10-16T15:00:00.000000Z",
    "total_credits": 200,
    "remaining_credits": 175,
    "status": true,
    "is_trial": false,
    "total_package_renewed": 0,
    "renewed_at": null,
    "is_canceled": false,
    "canceled_by": "none",
    "created_at": "2023-09-16T15:00:00.000000Z",
    "updated_at": "2023-09-22T14:45:00.000000Z",
    "package": {
        "id": 3,
        "package_name": "Merchant Business Pack",
        "package_details": "Credits for merchant operations.",
        "price": 100.00,
        "credits": 200,
        "validity": 30,
        "user_type": "merchant",
        "status": 1,
        "created_at": "2023-09-15T10:00:00.000000Z",
        "updated_at": "2023-09-15T10:00:00.000000Z"
    }
}
```

#### Error Response (`404 Not Found`)
Returned if the user has no subscriptions.

**Example Body**:
```json
{
    "message": "No subscription found"
}
```

#### Error Response (`401 Unauthorized`)
Returned if the request lacks a valid API token.

**Example Body**:
```json
{
    "message": "Unauthenticated."
}
```