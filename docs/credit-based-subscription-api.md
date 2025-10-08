# Credit-Based Subscription API Documentation

## Overview

This document covers all API endpoints that have been updated to support the new credit-based subscription system. Credits are now an access control mechanism that works alongside any payment method (cash, card, wallet, etc.).

---

## Table of Contents

1. [Order Endpoints](#order-endpoints)
2. [Ride Request Endpoints](#ride-request-endpoints)
3. [Delivery Man Endpoints](#delivery-man-endpoints)
4. [Credit Calculation Endpoints](#credit-calculation-endpoints)
5. [Models & Data Structures](#models--data-structures)

---

## Order Endpoints

### 1. Place New Order

**Endpoint:** `POST /api/v1/customer/order/place`

**Authentication:** Required (Bearer Token)

**Description:** Places a new order with automatic credit calculation and deduction for customer and merchant.

#### Request Headers
```
Authorization: Bearer {token}
moduleId: {module_id}
zoneId: [{zone_id}]
```

#### Request Body
```json
{
  "order_amount": 150.50,
  "payment_method": "cash_on_delivery",  // cash_on_delivery, digital_payment, wallet, offline_payment
  "order_type": "delivery",              // delivery, take_away, parcel
  "store_id": 123,
  "distance": 5.2,
  "address": "123 Main Street",
  "latitude": "40.7128",
  "longitude": "-74.0060",
  "contact_person_name": "John Doe",
  "contact_person_number": "+1234567890",
  "contact_person_email": "john@example.com",
  "cart": [...],                         // Cart items
  "dm_tips": 5.00,                       // Optional
  "schedule_at": "2025-10-05 14:00:00"   // Optional
}
```

#### Success Response (200 OK)
```json
{
  "message": "Order placed successfully",
  "order_id": 12345,
  "total_ammount": 155.50,
  "status": "confirmed",
  "created_at": "2025-10-04 12:30:45",
  "user_id": 789
}
```

#### Error Responses

**Insufficient Credits (403 Forbidden)**
```json
{
  "errors": [
    {
      "code": "insufficient_credits",
      "message": "Insufficient credit balance to place order"
    }
  ]
}
```

**Merchant Insufficient Credits (403 Forbidden)**
```json
{
  "errors": [
    {
      "code": "merchant_insufficient_credits",
      "message": "Merchant has insufficient credits"
    }
  ]
}
```

#### Credit Behavior
- **Customer Credits:** Deducted immediately upon order placement
- **Merchant Credits:** Deducted immediately upon order placement
- **Driver Credits:** Deducted when driver accepts the order
- **Credit Status:** Set to `deducted` for customer and merchant, `none` for driver

---

### 2. Cancel Order

**Endpoint:** `POST /api/v1/customer/order/cancel`

**Authentication:** Required (Bearer Token)

**Description:** Cancels an order and automatically refunds credits to all parties.

#### Request Body
```json
{
  "order_id": 12345,
  "reason": "Changed my mind",
  "guest_id": null  // Required if user is guest
}
```

#### Success Response (200 OK)
```json
{
  "message": "Order canceled successfully"
}
```

#### Credit Refund Behavior
- **Customer Credits:** Refunded if status was `deducted`
- **Driver Credits:** Refunded if status was `deducted`
- **Merchant Credits:** Refunded if status was `deducted`
- **Credit Status:** Changed from `deducted` to `refunded`

---

## Ride Request Endpoints

### 1. Create Ride Request

**Endpoint:** `POST /api/v1/customer/ride-request/store`

**Authentication:** Required (Bearer Token)

**Description:** Creates a new ride request with automatic credit calculation and deduction for customer.

#### Request Body
```json
{
  "pickup_lat": 40.7128,
  "pickup_lng": -74.0060,
  "pickup_address": "123 Main St, New York",
  "dropoff_lat": 40.7580,
  "dropoff_lng": -73.9855,
  "dropoff_address": "456 Broadway, New York",
  "estimated_time": 15,          // minutes
  "estimated_distance": 3.5,     // km
  "ride_category_id": 2,
  "rider_code": "123456"         // Optional: specific rider code
}
```

#### Success Response (200 OK)
```json
{
  "message": "Ride request placed successfully"
}
```

#### Error Responses

**Insufficient Credits (403 Forbidden)**
```json
{
  "errors": [
    {
      "code": "insufficient_credits",
      "message": "Insufficient credit balance to place ride request"
    }
  ]
}
```

**Rider Not Available (400 Bad Request)**
```json
{
  "message": "Deliveryman does not exists or not available at the moment."
}
```

#### Credit Behavior
- **Customer Credits:** Deducted immediately upon ride request creation
- **Driver Credits:** Deducted when driver accepts the ride
- **Credit Status:** `customer_credits_status` set to `deducted`, `driver_credits_status` set to `none`

---

### 2. Cancel Ride Request

**Endpoint:** `PUT /api/v1/customer/ride-request/update/{ride_request_id}`

**Authentication:** Required (Bearer Token)

**Description:** Cancels a ride request and refunds credits.

#### Request Body
```json
{
  "status": "canceled"
}
```

#### Success Response (200 OK)
```json
{
  "message": "Ride request canceled successfully"
}
```

#### Credit Refund Behavior
- **Customer Credits:** Refunded if status was `deducted`
- **Driver Credits:** Refunded if status was `deducted`
- **Credit Status:** Changed to `refunded`

---

### 3. Calculate Ride Credits

**Endpoint:** `POST /api/v1/customer/ride-request/calculate-credits`

**Authentication:** Required (Bearer Token)

**Description:** Calculates required credits for a ride request before placing it.

#### Request Body
```json
{
  "estimated_fare": 25.50,
  "estimated_distance": 5.2,
  "user_type": "customer"  // customer or driver
}
```

#### Success Response (200 OK)
```json
{
  "credits_required": 3.50,
  "rule_applied": {
    "name": "Standard Ride Credit Rule",
    "condition_type": "price_range",
    "rule_id": 15
  },
  "calculation_details": {
    "estimated_fare": 25.50,
    "estimated_distance": 5.2,
    "user_type": "customer"
  }
}
```

---

## Delivery Man Endpoints

### 1. Get Latest Orders (Driver)

**Endpoint:** `GET /api/v1/deliveryman/order/list`

**Authentication:** Required (Token parameter)

**Description:** Gets available orders filtered by driver's credit balance. Only shows orders the driver can afford.

#### Query Parameters
```
token={deliveryman_token}
```

#### Success Response (200 OK)
```json
[
  {
    "id": 12345,
    "order_amount": 150.50,
    "order_status": "confirmed",
    "order_type": "delivery",
    "driver_credits_required": 2.50,
    "customer": {
      "id": 789,
      "f_name": "John",
      "l_name": "Doe"
    },
    "store": {
      "id": 123,
      "name": "Pizza Palace"
    },
    "delivery_address": {...},
    "schedule_at": "2025-10-04 14:00:00"
  }
]
```

#### Filtering Logic
- Only shows orders where `driver_credits_required <= driver's credit_balance`
- Respects zone, vehicle type, and other existing filters

---

### 2. Get New Ride Requests (Driver)

**Endpoint:** `GET /api/v1/deliveryman/ride-request/list`

**Authentication:** Required (Token parameter)

**Description:** Gets available ride requests filtered by driver's credit balance.

#### Query Parameters
```
token={deliveryman_token}
limit=10    // Optional, default: 10
offset=1    // Optional, default: 1
```

#### Success Response (200 OK)
```json
{
  "limit": 10,
  "offset": 1,
  "total_size": 5,
  "data": [
    {
      "id": 456,
      "pickup_address": "123 Main St",
      "dropoff_address": "456 Broadway",
      "estimated_fare": 25.50,
      "estimated_distance": 3.5,
      "driver_credits_required": 2.00,
      "ride_status": "pending",
      "customer": {
        "id": 789,
        "f_name": "Jane",
        "l_name": "Smith"
      }
    }
  ]
}
```

#### Filtering Logic
- Only shows rides where `driver_credits_required <= driver's credit_balance`
- Filters by zone, vehicle category, and proximity (20km radius)

---

### 3. Accept Order (Driver)

**Endpoint:** `POST /api/v1/deliveryman/order/accept`

**Authentication:** Required (Token parameter)

**Description:** Accepts an order and deducts driver credits.

#### Request Body
```json
{
  "token": "{deliveryman_token}",
  "order_id": 12345
}
```

#### Success Response (200 OK)
```json
{
  "message": "Order accepted successfully"
}
```

#### Error Responses

**Insufficient Credits (403 Forbidden)**
```json
{
  "errors": [
    {
      "code": "insufficient_credits",
      "message": "Insufficient credit balance to accept order"
    }
  ]
}
```

**Order Already Accepted (404 Not Found)**
```json
{
  "errors": [
    {
      "code": "order",
      "message": "Can not accept"
    }
  ]
}
```

#### Credit Behavior
- **Driver Credits:** Deducted immediately upon acceptance
- **Driver Credit Status:** Changed from `none` to `deducted`
- Uses pessimistic locking to prevent race conditions

---

### 4. Update Ride Request Status (Driver)

**Endpoint:** `POST /api/v1/deliveryman/ride-request/update`

**Authentication:** Required (Token parameter)

**Description:** Updates ride request status (accept, cancel, complete).

#### Request Body
```json
{
  "token": "{deliveryman_token}",
  "ride_request_id": 456,
  "status": "accepted",  // accepted, canceled, picked_up, completed
  "lat": 40.7128,        // Required for picked_up, completed, canceled
  "lng": -74.0060,       // Required for picked_up, completed, canceled
  "distance": 3.8,       // Required for completed, canceled
  "otp": "1234"          // Required for picked_up
}
```

#### Success Response (200 OK)
```json
{
  "message": "Ride request status updated successfully"
}
```

#### Error Responses

**Insufficient Credits (403 Forbidden)** - When accepting
```json
{
  "errors": [
    {
      "code": "insufficient_credits",
      "message": "Insufficient credit balance to accept ride"
    }
  ]
}
```

**Invalid OTP (406 Not Acceptable)** - When picking up
```json
{
  "errors": [
    {
      "code": "otp",
      "message": "OTP not matched"
    }
  ]
}
```

#### Credit Behavior

**On Accept:**
- Driver credits deducted immediately
- `driver_credits_status` changed from `none` to `deducted`

**On Cancel:**
- Customer credits refunded if `customer_credits_status` is `deducted`
- Driver credits refunded if `driver_credits_status` is `deducted`
- Both statuses changed to `refunded`

**On Complete:**
- No credit changes (credits remain deducted)

---

### 5. Update Order Status (Driver)

**Endpoint:** `POST /api/v1/deliveryman/order/update-status`

**Authentication:** Required (Token parameter)

**Description:** Updates order status and handles credit refunds on cancellation.

#### Request Body
```json
{
  "token": "{deliveryman_token}",
  "order_id": 12345,
  "status": "picked_up",  // confirmed, picked_up, delivered, canceled
  "reason": "Customer unavailable",  // Required if status is 'canceled'
  "otp": "1234",                     // Required if status is 'delivered'
  "order_proof": [...]               // Optional files for delivered status
}
```

#### Success Response (200 OK)
```json
{
  "message": "Status updated"
}
```

#### Credit Refund Behavior (When Canceled)
- **Customer Credits:** Refunded if status was `deducted`
- **Driver Credits:** Refunded if status was `deducted`
- **Merchant Credits:** Refunded if status was `deducted`
- All credit statuses changed to `refunded`

---

## Credit Calculation Endpoints

### 1. Calculate Order Credits

**Endpoint:** `POST /api/v1/customer/order/calculate-credits`

**Authentication:** Required (Bearer Token)

**Description:** Calculates required credits for an order before placing it.

#### Request Body
```json
{
  "price": 150.50,
  "distance": 5.2,       // Optional
  "module_id": 1,        // Optional
  "user_type": "customer"  // Optional: customer, driver, merchant (default: customer)
}
```

#### Success Response (200 OK)
```json
{
  "credits_required": 5.00,
  "rule_applied": {
    "name": "High Value Order Rule",
    "condition_type": "price_range",
    "rule_id": 42
  },
  "calculation_details": {
    "price": 150.50,
    "distance": 5.2,
    "user_type": "customer",
    "module_id": 1
  }
}
```

#### Response When No Rule Found
```json
{
  "credits_required": 0.00,
  "rule_applied": {
    "name": null,
    "condition_type": null,
    "rule_id": null
  },
  "calculation_details": {
    "price": 150.50,
    "distance": 5.2,
    "user_type": "customer",
    "module_id": 1
  }
}
```

---

## Models & Data Structures

### Order Model

#### New Fields
```php
[
    'customer_credits_required' => 'decimal:2',
    'driver_credits_required' => 'decimal:2',
    'merchant_credits_required' => 'decimal:2',
    'customer_credits_status' => 'enum:none,pending,deducted,refunded',
    'driver_credits_status' => 'enum:none,pending,deducted,refunded',
    'merchant_credits_status' => 'enum:none,pending,deducted,refunded',
]
```

#### Default Values
```php
[
    'customer_credits_required' => 0,
    'driver_credits_required' => 0,
    'merchant_credits_required' => 0,
    'customer_credits_status' => 'none',
    'driver_credits_status' => 'none',
    'merchant_credits_status' => 'none',
]
```

### RideRequest Model

#### New Fields
```php
[
    'customer_credits_required' => 'decimal:2',
    'driver_credits_required' => 'decimal:2',
    'customer_credits_status' => 'enum:none,pending,deducted,refunded',
    'driver_credits_status' => 'enum:none,pending,deducted,refunded',
]
```

#### Default Values
```php
[
    'customer_credits_required' => 0,
    'driver_credits_required' => 0,
    'customer_credits_status' => 'none',
    'driver_credits_status' => 'none',
]
```

### Credit Status Flow

#### Customer/Merchant Flow
```
none → deducted → refunded (on cancellation)
                ↓
            (completed - stays deducted)
```

#### Driver Flow
```
none → deducted (on accept) → refunded (on cancellation)
                            ↓
                        (completed - stays deducted)
```

---

## Credit Deduction Rules

### Rule Structure
```json
{
  "id": 1,
  "name": "Standard Order Rule",
  "user_type": "customer",  // customer, driver, merchant
  "module_id": 1,           // null for global rules
  "condition_type": "price_range",  // price_range or distance_range
  "min_value": 50.00,
  "max_value": 100.00,      // null for unlimited
  "credits_to_deduct": 3.50,
  "status": true
}
```

### Rule Matching Logic
1. First tries to find rule by `price_range`
2. If not found and order type is delivery/parcel, tries `distance_range`
3. If no rule found, returns 0 credits (no charge)
4. Module-specific rules take precedence over global rules

---

## Error Codes Reference

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `insufficient_credits` | 403 | User doesn't have enough credits |
| `merchant_insufficient_credits` | 403 | Merchant doesn't have enough credits |
| `merchant_not_found` | 403 | Store has no associated merchant |
| `order` | 404 | Order not found or cannot be accepted |
| `calculation_error` | 500 | Error calculating credits |
| `otp` | 406 | OTP verification failed |
| `ride_request_id` | 403 | Ride request not found |

---

## Important Notes

### Credit vs Payment Method
- **Credits are NOT a payment method** - they are an access control mechanism
- Credits work alongside ANY payment method (cash, card, wallet, etc.)
- Example: A customer can pay with cash but still needs credits to place the order

### Timing of Credit Deductions
- **Customer:** Immediately on order/ride placement
- **Merchant:** Immediately on order placement
- **Driver:** On order/ride acceptance (not on placement)

### Credit Filtering
- Drivers only see orders/rides where `required_credits <= driver's credit_balance`
- This filtering happens at the query level for performance
- Push notifications only sent to drivers with sufficient credits

### Race Condition Prevention
- Uses pessimistic locking when deducting driver credits
- Prevents multiple drivers from accepting the same order simultaneously
- First driver with sufficient credits to accept wins

### Refund Scenarios
- **Customer Cancels:** All parties (customer, driver, merchant) get refunds
- **Driver Cancels:** All parties get refunds
- **Order Delivered:** No refunds, credits stay deducted
- **Ride Completed:** No refunds, credits stay deducted

---

## Database Indexes

For optimal query performance, the following indexes are created:

### Orders Table
- `driver_credits_required` (for filtering)
- Composite: `(customer_credits_status, order_status)` (for refund queries)
- Composite: `(driver_credits_status, order_status)` (for refund queries)
- Composite: `(merchant_credits_status, order_status)` (for refund queries)

### Ride Requests Table
- `driver_credits_required` (for filtering)
- Composite: `(customer_credits_status, ride_status)` (for refund queries)
- Composite: `(driver_credits_status, ride_status)` (for refund queries)

---

## Migration History

1. `2025_10_04_085554_add_credit_fields_to_orders_table.php` - Added 6 credit fields to orders
2. `2025_10_04_085639_add_credit_fields_to_ride_requests_table.php` - Added 4 credit fields to ride requests
3. `2025_10_04_091304_add_credit_status_indexes_to_orders_and_ride_requests.php` - Added performance indexes

---

## Testing Checklist

### Order Flow
- [ ] Customer with sufficient credits can place order
- [ ] Customer with insufficient credits cannot place order
- [ ] Merchant with insufficient credits blocks order
- [ ] Driver sees only affordable orders
- [ ] Driver can accept order if has credits
- [ ] Driver cannot accept if insufficient credits
- [ ] Credits refunded on customer cancellation
- [ ] Credits refunded on driver cancellation
- [ ] Credits NOT refunded on successful delivery

### Ride Request Flow
- [ ] Customer with credits can request ride
- [ ] Customer without credits cannot request ride
- [ ] Driver sees only affordable rides
- [ ] Driver can accept ride with credits
- [ ] Driver cannot accept without credits
- [ ] Credits refunded on customer cancellation
- [ ] Credits refunded on driver cancellation
- [ ] Credits NOT refunded on completion

### Credit Calculation
- [ ] Price range rules work correctly
- [ ] Distance range rules work correctly
- [ ] Module-specific rules take precedence
- [ ] Returns 0 when no rules match
- [ ] Calculate endpoints return correct values

---

## Support & Questions

For questions about the credit-based subscription system:
- Check the `CreditDeductionRule` model for active rules
- Review the `CreditRefundService` for refund logic
- Check the `WalletService` for credit transaction handling

Last Updated: October 4, 2025
