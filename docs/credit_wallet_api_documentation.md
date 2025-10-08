# Credit Wallet Payment Method - API Documentation

## Overview

This documentation covers the new Credit Wallet payment method integration and the Calculate Credits API endpoint. The credit wallet system allows users to pay for orders using credits from their subscription packages instead of traditional payment methods.

## API Changes Summary

### 1. New Payment Method: `credit_wallet`
The order placement API now supports a new payment method called `credit_wallet` alongside existing methods.

### 2. New API Endpoint: Calculate Credits
A new endpoint to calculate required credits before placing an order.

---

## 1. Calculate Credits API

### Endpoint
```
POST /api/v1/customer/order/calculate-credits
```

### Description
Calculate the number of credits required for an order based on price, distance, module, and user type. This endpoint helps mobile apps show users the exact credit cost before placing an order.

### Request Headers
```
Content-Type: application/json
X-localization: en (or user's preferred language)
```

### Request Parameters

| Parameter   | Type    | Required | Description                           |
|-------------|---------|----------|---------------------------------------|
| price       | numeric | Yes      | Order amount/price (minimum: 0)      |
| distance    | numeric | No       | Delivery distance in km (minimum: 0) |
| module_id   | integer | No       | Module ID (must exist in modules table) |
| order_type  | string  | No       | Order type: `take_away`, `delivery`, `parcel` (default: `delivery`) |
| user_type   | string  | No       | User type: `customer`, `driver`, `merchant` (default: `customer`) |

### Request Example
```json
{
  "price": 25.50,
  "distance": 5.2,
  "module_id": 1,
  "order_type": "delivery",
  "user_type": "customer"
}
```

### Success Response (200)
```json
{
  "credits_required": 2.0,
  "rule_applied": {
    "name": "Standard Delivery Rate",
    "condition_type": "price_range",
    "rule_id": 1
  },
  "calculation_details": {
    "price": 25.50,
    "distance": 5.2,
    "user_type": "customer",
    "order_type": "delivery",
    "module_id": 1
  }
}
```

### Error Response (403)
```json
{
  "errors": [
    {
      "code": "price",
      "message": "The price field is required."
    }
  ]
}
```

### Error Response (500)
```json
{
  "errors": [
    {
      "code": "calculation_error",
      "message": "Failed to calculate credits. Please try again."
    }
  ]
}
```

---

## 2. Order Placement with Credit Wallet

### Endpoint
```
POST /api/v1/customer/order/place
```

### Updated Payment Method Support
The existing order placement API now supports `credit_wallet` as a payment method.

### Request Parameters (Updated)
The `payment_method` parameter now accepts:

| Value           | Description                    |
|-----------------|--------------------------------|
| cash_on_delivery| Cash payment on delivery       |
| digital_payment | Online payment gateway         |
| wallet          | Regular wallet balance         |
| offline_payment | Offline payment method         |
| **credit_wallet**| **Credit-based payment (NEW)** |

### Request Example
```json
{
  "payment_method": "credit_wallet",
  "order_type": "delivery",
  "order_amount": 25.50,
  "distance": 5.2,
  "store_id": 1,
  "address": "123 Main Street",
  "latitude": 40.7128,
  "longitude": -74.0060,
  // ... other order fields
}
```

### Behavior Changes for Credit Wallet
When `payment_method` is set to `credit_wallet`:

1. **Credit Validation**: System checks if user has sufficient credit balance
2. **Dynamic Calculation**: Credits are calculated using configurable rules
3. **Order Status**: Order is automatically set to `confirmed` status
4. **Payment Status**: Order payment status is set to `paid`
5. **Credit Deduction**: Credits are deducted from user's credit wallet upon successful order creation

### Credit Wallet Error Responses

#### Insufficient Credits (403)
```json
{
  "errors": [
    {
      "code": "insufficient_credits",
      "message": "Insufficient credit balance to place this order."
    }
  ]
}
```

---

## 3. User Credit Balance

### Get User Info API
```
GET /api/v1/customer/info
```

The existing user info API now includes credit balance information:

### Response (Updated)
```json
{
  "id": 1,
  "f_name": "John",
  "l_name": "Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "wallet_balance": 150.00,
  "credit_balance": 25.5,  // NEW: Credit wallet balance
  "loyalty_point": 100,
  // ... other user fields
}
```

---

## 4. Credit Wallet Management APIs

### Get Credit Wallet Balance
```
GET /api/v1/credit-wallet/
```

### Headers
```
Authorization: Bearer {user_token}
X-localization: en
```

### Response
```json
{
  "balance": "25.5",
  "currency_symbol": "$",
  "last_updated": "2025-01-01T12:00:00Z"
}
```

### Get Credit Transactions
```
GET /api/v1/credit-wallet/transactions
```

### Query Parameters
| Parameter | Type    | Required | Description           |
|-----------|---------|----------|-----------------------|
| limit     | integer | No       | Number of records (default: 10) |

### Response
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "amount": -2.0,
      "transaction_type": "usage",
      "details": "Order payment - Order #12345",
      "created_at": "2025-01-01T10:30:00Z",
      "reference_type": "App\\Models\\Order",
      "reference_id": 12345
    }
  ],
  "total": 50,
  "per_page": 10
}
```

---

## 5. Implementation Guide for Mobile Apps

### Step 1: Check Credit Balance
Before showing credit wallet as a payment option, check user's credit balance:

```dart
// Example for Flutter
Future<double> getCreditBalance() async {
  final response = await http.get('/api/v1/customer/info');
  final data = json.decode(response.body);
  return data['credit_balance'];
}
```

### Step 2: Calculate Credits for Order
Before order placement, calculate required credits:

```dart
Future<double> calculateOrderCredits(double price, double distance) async {
  final response = await http.post(
    '/api/v1/customer/order/calculate-credits',
    body: json.encode({
      'price': price,
      'distance': distance,
      'order_type': 'delivery',
    }),
  );
  final data = json.decode(response.body);
  return data['credits_required'];
}
```

### Step 3: Validate Before Order
```dart
bool canPayWithCredits(double userBalance, double requiredCredits) {
  return userBalance >= requiredCredits;
}
```

### Step 4: Place Order with Credit Wallet
```dart
Future<void> placeOrderWithCredits() async {
  final response = await http.post(
    '/api/v1/customer/order/place',
    body: json.encode({
      'payment_method': 'credit_wallet',
      // ... other order data
    }),
  );
}
```

---

## 6. Error Handling Guide

### Credit-Related Errors

| Error Code            | HTTP Status | Description                    | Action Required                |
|-----------------------|-------------|--------------------------------|--------------------------------|
| insufficient_credits  | 403         | Not enough credit balance     | Show balance, suggest top-up  |
| calculation_error     | 500         | Credit calculation failed     | Retry or use different payment |

### Example Error Handling
```dart
try {
  await placeOrder();
} catch (e) {
  if (e.code == 'insufficient_credits') {
    showDialog('Insufficient Credits', 'Please add credits to your wallet');
  }
}
```

---

## 7. Testing Scenarios

### Test Cases for Mobile App QA

1. **Credit Calculation**
   - Test with different price ranges
   - Test with different distances
   - Test with different order types
   - Verify calculations match expected rules

2. **Credit Wallet Payment**
   - Test successful order with sufficient credits
   - Test order rejection with insufficient credits
   - Verify credit deduction after successful order
   - Test error handling for API failures

3. **Balance Display**
   - Verify credit balance shows correctly in user profile
   - Test credit balance updates after transactions
   - Verify currency formatting

4. **Edge Cases**
   - Test with zero credit balance
   - Test with exact credit balance matching order cost
   - Test with invalid parameters in calculate-credits API

---

## 8. Configuration Notes

### Admin Configuration
- Credit deduction rules are configurable through admin panel
- Rules can be based on:
  - Price ranges (e.g., $0-$30 = 1 credit)
  - Distance ranges (e.g., 0-5km = 2 credits)
  - User types (customer/driver/merchant)
  - Service modules

### Fallback Logic
If no configured rules match, the system uses fallback logic:
- Orders < $11: 4 credits
- Orders $11-$30: 1 credit
- Orders $31-$50: 2 credits
- Orders $51-$100: 3 credits
- Orders > $100: 5 credits

---

## 9. Changelog

### Version 1.1.0 (Current)
- ✅ Added `credit_wallet` payment method support
- ✅ Added `/api/v1/customer/order/calculate-credits` endpoint
- ✅ Enhanced user info API with `credit_balance` field
- ✅ Added credit wallet management APIs
- ✅ Implemented dynamic credit calculation using admin rules
- ✅ Added proper error handling for credit-related operations

### Migration Notes
- No breaking changes to existing APIs
- New payment method is additive
- Existing payment flows remain unchanged
- Credit wallet APIs are optional features

---

This documentation provides all the necessary information for mobile app developers to implement the credit wallet payment method. The APIs are designed to be intuitive and follow the same patterns as existing payment methods while providing the flexibility needed for credit-based transactions.
