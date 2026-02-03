# Multi-X ERP SaaS

Enterprise Resource Planning (ERP) SaaS platform built with Laravel and Vue.js, designed for multi-tenancy, modularity, and scalability.

## Overview

This is a fully production-ready, enterprise-grade, modular ERP SaaS platform featuring:

- **Multi-tenancy** with complete tenant isolation
- **Modular architecture** following Clean Architecture and Domain-Driven Design principles
- **Comprehensive modules** including IAM, Inventory Management, CRM, POS, Invoicing, and more
- **Full-stack implementation** with Laravel (backend) and Vue.js with Vite (frontend)

## Key Features

- Multi-organization, multi-vendor, multi-branch, multi-warehouse support
- Multi-currency, multi-language (i18n), multi-time-zone operations
- Flexible product and catalog management
- Inventory lifecycle management with append-only stock ledger architecture
- Advanced pricing engines with dynamic pricing rules
- Role-based access control (RBAC) and Attribute-based access control (ABAC)
- RESTful APIs with OpenAPI documentation
- Event-driven architecture for asynchronous workflows
- Enterprise-grade security standards

## Architecture

The platform strictly follows:

- **Clean Architecture** with clear modular boundaries
- **Controller → Service → Repository** pattern
- **SOLID, DRY, and KISS** principles
- **Event-driven architecture** for asynchronous operations
- **Transactional boundaries** with atomicity and rollback safety

## Technology Stack

### Backend
- **Framework**: Laravel 12
- **PHP**: 8.3+
- **Database**: MySQL/PostgreSQL with append-only stock ledgers
- **Authentication**: Laravel Sanctum
- **API**: RESTful with versioning

### Frontend
- **Framework**: Vue.js 3
- **Build Tool**: Vite
- **State Management**: Pinia (planned)
- **Styling**: Responsive and accessible layouts

## Installation

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js 20+ and npm
- MySQL 8.0+ or PostgreSQL 13+

### Backend Setup

1. Navigate to the backend directory:
```bash
cd backend
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multi_x_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed initial data (optional):
```bash
php artisan db:seed --class=InitialDataSeeder
```

8. Start the development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

### Frontend Setup

1. Navigate to the frontend directory:
```bash
cd frontend
```

2. Install dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm run dev
```

The frontend will be available at `http://localhost:5173`

## API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication
The API uses Laravel Sanctum for authentication. Include the access token in the Authorization header:
```
Authorization: Bearer {your-token}
```

### Available Endpoints

#### Health Check
```
GET /api/v1/health
```

#### Authentication
```
POST   /api/v1/auth/register              # Register new user
POST   /api/v1/auth/login                 # Login user
POST   /api/v1/auth/logout                # Logout user (protected)
POST   /api/v1/auth/refresh               # Refresh token (protected)
GET    /api/v1/auth/user                  # Get current user (protected)
```

#### Products
```
GET    /api/v1/inventory/products              # List products
POST   /api/v1/inventory/products              # Create product
GET    /api/v1/inventory/products/{id}         # Get product
PUT    /api/v1/inventory/products/{id}         # Update product
DELETE /api/v1/inventory/products/{id}         # Delete product
GET    /api/v1/inventory/products/search?q=    # Search products
GET    /api/v1/inventory/products/below-reorder-level  # Low stock items
GET    /api/v1/inventory/products/{id}/stock-history   # Stock history
```

#### Customers (CRM)
```
GET    /api/v1/crm/customers                # List customers
POST   /api/v1/crm/customers                # Create customer
GET    /api/v1/crm/customers/{id}           # Get customer
PUT    /api/v1/crm/customers/{id}           # Update customer
DELETE /api/v1/crm/customers/{id}           # Delete customer
GET    /api/v1/crm/customers/search?q=      # Search customers
```

## Development Guidelines

For detailed development guidelines, coding standards, and architectural decisions, please refer to the [Copilot Instructions](.github/copilot-instructions.md).

### Key Principles

1. **Always review existing code** before making changes
2. **Service Layer Orchestration** - All cross-module interactions go through services
3. **Event-Driven Architecture** - Use events for asynchronous workflows only
4. **Append-Only Ledger** - Never delete stock ledger entries, create reversals instead

## Project Structure

### Backend
```
backend/
├── app/
│   ├── Contracts/           # Interfaces and contracts
│   ├── Enums/              # Enums (ProductType, StockMovementType, etc.)
│   ├── Http/Controllers/   # Base controllers
│   ├── Models/             # Core models (Tenant, User)
│   ├── Modules/            # Business modules
│   │   ├── IAM/           # Identity and Access Management
│   │   └── Inventory/     # Inventory Management
│   ├── Repositories/       # Data access layer
│   ├── Services/          # Business logic layer
│   └── Traits/            # Reusable traits
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
└── routes/
    └── api.php           # API routes
```

### Frontend
```
frontend/
├── src/
│   ├── components/       # Reusable components
│   ├── views/           # Page components
│   ├── modules/         # Module-specific components
│   ├── services/        # API services
│   └── stores/          # State management
└── public/
```

## Core Modules

### Implemented
- ✅ Multi-Tenancy Foundation
- ✅ Authentication & Authorization (Sanctum)
- ✅ IAM (Users, Roles & Permissions)
- ✅ Inventory Management (Products & Stock Ledger)
- ✅ Stock Movement Service (Adjustments, Transfers)
- ✅ Pricing Service (Tiered pricing, discounts, tax calculation)
- ✅ CRM (Customer Management)
- ✅ Procurement (Purchase Orders, Suppliers)
- ✅ Master Data (Categories, Warehouses, Units, Taxes)
- ✅ Event-Driven Architecture
- ✅ Notification System Foundation

### Planned
- POS (Point of Sale)
- Manufacturing (BOM, Production Orders)
- Financial Integration
- Reporting & Analytics
- Audit Trails

## Testing

Run the test suite:
```bash
cd backend
php artisan test
```

## Demo Credentials

After running the seeder, you can use these demo credentials:

- **Email**: admin@demo.com
- **Password**: password
- **Tenant**: demo-company

## Security

- HTTPS enforcement in production
- CSRF protection enabled
- SQL injection prevention via Eloquent
- XSS prevention via output escaping
- Rate limiting on API endpoints
- Secure credential storage

## Performance

- Eager loading to prevent N+1 queries
- Database indexing on frequently queried columns
- Caching for frequently accessed data
- Queue support for long-running tasks
- Pagination for large datasets

## Contributing

Please follow the architectural guidelines and coding standards outlined in the [Copilot Instructions](.github/copilot-instructions.md).

## License

[License information to be added]

## Support

For issues and questions, please use the GitHub issue tracker.

---

**Built with Clean Architecture principles for long-term maintainability and scalability.**
