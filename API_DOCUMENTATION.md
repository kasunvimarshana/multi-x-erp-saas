# Multi-X ERP SaaS - API Documentation

## Overview

The Multi-X ERP SaaS platform provides a comprehensive RESTful API for managing all aspects of enterprise resource planning.

**Base URL:** `http://localhost:8000/api/v1`

**API Version:** v1

**Authentication:** Bearer Token (Laravel Sanctum)

## Authentication

All protected endpoints require a valid authentication token in the Authorization header:

```
Authorization: Bearer {your-token}
```

### Register User

**Endpoint:** `POST /api/v1/auth/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "tenant_slug": "demo-company"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "tenant_id": 1
    },
    "token": "1|abc123..."
  },
  "message": "User registered successfully"
}
```

### Login

**Endpoint:** `POST /api/v1/auth/login`

**Request Body:**
```json
{
  "email": "admin@demo.com",
  "password": "password",
  "tenant_slug": "demo-company"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@demo.com",
      "roles": [
        {
          "id": 1,
          "name": "Administrator",
          "slug": "admin",
          "permissions": [...]
        }
      ]
    },
    "token": "1|abc123..."
  },
  "message": "Login successful"
}
```

### Logout

**Endpoint:** `POST /api/v1/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

### Get Current User

**Endpoint:** `GET /api/v1/auth/user`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@demo.com",
    "tenant": {...},
    "roles": [...]
  }
}
```

### Refresh Token

**Endpoint:** `POST /api/v1/auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "2|xyz789..."
  },
  "message": "Token refreshed successfully"
}
```

## Inventory Management

### Products

#### List Products

**Endpoint:** `GET /api/v1/inventory/products`

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Results per page (default: 15)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "sku": "PROD-001",
        "name": "Product Name",
        "product_type": "inventory",
        "buying_price": 100.00,
        "selling_price": 150.00,
        "current_stock": 50
      }
    ],
    "total": 10,
    "per_page": 15
  }
}
```

#### Create Product

**Endpoint:** `POST /api/v1/inventory/products`

**Request Body:**
```json
{
  "sku": "PROD-002",
  "name": "New Product",
  "product_type": "inventory",
  "category_id": 1,
  "unit_id": 1,
  "buying_price": 100.00,
  "selling_price": 150.00,
  "reorder_level": 10,
  "description": "Product description",
  "is_active": true
}
```

#### Search Products

**Endpoint:** `GET /api/v1/inventory/products/search?q=laptop`

**Query Parameters:**
- `q` - Search term
- `page` - Page number
- `per_page` - Results per page

#### Get Low Stock Items

**Endpoint:** `GET /api/v1/inventory/products/below-reorder-level`

#### Get Stock History

**Endpoint:** `GET /api/v1/inventory/products/{id}/stock-history`

## CRM Module

### Customers

#### List Customers

**Endpoint:** `GET /api/v1/crm/customers`

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "customer_type": "individual",
        "is_active": true
      }
    ]
  }
}
```

#### Create Customer

**Endpoint:** `POST /api/v1/crm/customers`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "customer_type": "individual",
  "billing_address": "123 Main St",
  "billing_city": "New York",
  "billing_country": "USA",
  "credit_limit": 10000.00,
  "payment_terms_days": 30,
  "is_active": true
}
```

#### Search Customers

**Endpoint:** `GET /api/v1/crm/customers/search?q=john`

## Error Responses

### Validation Error (422)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

### Unauthorized (401)

```json
{
  "success": false,
  "message": "Unauthenticated"
}
```

### Not Found (404)

```json
{
  "success": false,
  "message": "Resource not found"
}
```

### Server Error (500)

```json
{
  "success": false,
  "message": "An error occurred while processing your request"
}
```

## Rate Limiting

API requests are rate-limited to prevent abuse:
- **Authentication endpoints:** 5 requests per minute
- **All other endpoints:** 60 requests per minute

## Pagination

All list endpoints support pagination with the following query parameters:
- `page` - The page number (default: 1)
- `per_page` - Number of results per page (default: 15, max: 100)

Paginated responses include:
```json
{
  "current_page": 1,
  "data": [...],
  "first_page_url": "...",
  "last_page_url": "...",
  "next_page_url": "...",
  "prev_page_url": null,
  "per_page": 15,
  "total": 100
}
```

## Best Practices

1. **Always include the Authorization header** for protected endpoints
2. **Handle errors gracefully** by checking the `success` field
3. **Implement retry logic** for failed requests
4. **Use pagination** for large datasets
5. **Validate data** on the client side before sending to API
6. **Store tokens securely** (never in localStorage for production apps)
7. **Implement token refresh** before expiration

## Support

For issues and questions, please use the GitHub issue tracker.
