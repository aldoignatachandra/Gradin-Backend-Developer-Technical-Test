# Gradin Backend Developer Technical Test

> Backend technical test implementation for Gradin - Courier CRUD API built with Laravel 13, following best practices and clean code principles.

## Overview

A RESTful API for managing courier data with full CRUD operations, advanced search, filtering, sorting, and pagination. Built with Laravel 13 and PHP 8.4, following industry best practices.

### Key Features

- **CRUD Operations** - Create, Read, Update, Delete couriers
- **Advanced Search** - Multi-word name search (e.g., `?search=budi+agung` matches "Budiono Hadi Agung")
- **Level Filtering** - Filter by courier level (e.g., `?level=2,3`)
- **Flexible Sorting** - Sort by name (default) or registration date
- **Pagination** - Configurable page size with metadata
- **Input Validation** - Comprehensive validation with Form Request classes
- **Feature Tests** - 15 tests with 163 assertions

## Tech Stack

| Component  | Technology           |
| ---------- | -------------------- |
| Framework  | Laravel 13.9.0       |
| PHP        | 8.4                  |
| Database   | SQLite (development) |
| Testing    | PHPUnit 12           |
| Code Style | Laravel Pint         |

## API Endpoints

### Base URL

```
/api/couriers
```

### Endpoints

| Method   | Endpoint             | Description                   |
| -------- | -------------------- | ----------------------------- |
| `GET`    | `/api/couriers`      | List all couriers (paginated) |
| `GET`    | `/api/couriers/{id}` | Get single courier            |
| `POST`   | `/api/couriers`      | Create new courier            |
| `PUT`    | `/api/couriers/{id}` | Update courier                |
| `DELETE` | `/api/couriers/{id}` | Delete courier                |

### Query Parameters (GET /api/couriers)

| Parameter   | Type    | Description                           | Example               |
| ----------- | ------- | ------------------------------------- | --------------------- |
| `search`    | string  | Search by name (multi-word)           | `?search=budi+agung`  |
| `level`     | string  | Filter by level (comma-separated)     | `?level=2,3`          |
| `sort`      | string  | Sort field: `name` or `registered_at` | `?sort=registered_at` |
| `direction` | string  | Sort direction: `asc` or `desc`       | `?direction=desc`     |
| `per_page`  | integer | Items per page (default: 15)          | `?per_page=10`        |

### Example Requests

**Search and filter:**

```
GET /api/couriers?search=budi&level=2,3&sort=name&direction=asc
```

**Pagination:**

```
GET /api/couriers?per_page=10&page=2
```

## Database Schema

### Couriers Table

| Column          | Type      | Constraints                 |
| --------------- | --------- | --------------------------- |
| `id`            | bigint    | Primary key, auto-increment |
| `name`          | string    | Required                    |
| `email`         | string    | Required, unique            |
| `phone`         | string    | Required                    |
| `level`         | tinyint   | Required, 1-5, unsigned     |
| `address`       | text      | Nullable                    |
| `is_active`     | boolean   | Default: true               |
| `registered_at` | timestamp | Nullable                    |
| `created_at`    | timestamp | Auto                        |
| `updated_at`    | timestamp | Auto                        |

**Indexes:** `level`, `name`, `registered_at`

## Installation

### Prerequisites

- PHP 8.3+
- Composer
- SQLite (or configure other database in `.env`)

### Setup

```bash
# Clone repository
git clone https://github.com/aldoignatachandra/Gradin-Backend-Developer-Technical-Test.git
cd Gradin-Backend-Developer-Technical-Test

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
touch database/database.sqlite
php artisan migrate --seed

# Start development server
php artisan serve
```

### Run Tests

```bash
# Run all tests
php artisan test

# Run with compact output
php artisan test --compact

# Run specific test file
php artisan test --compact tests/Feature/CourierControllerTest.php
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── CourierController.php      # CRUD operations
│   └── Requests/
│       ├── StoreCourierRequest.php    # Create validation
│       └── UpdateCourierRequest.php   # Update validation
├── Models/
│   └── Courier.php                    # Eloquent model

database/
├── factories/
│   └── CourierFactory.php             # Test data factory
├── migrations/
│   └── create_couriers_table.php      # Database schema
└── seeders/
    └── CourierSeeder.php              # Sample data

routes/
└── api.php                            # API routes

tests/
└── Feature/
    └── CourierControllerTest.php      # Feature tests
```

## Best Practices Applied

### Architecture

- ✅ **Form Request Validation** - Separate validation classes
- ✅ **Implicit Route Model Binding** - Automatic model resolution
- ✅ **API Resources** - RESTful conventions with `apiResource()`
- ✅ **Thin Controllers** - Business logic separated

### Database

- ✅ **Indexing** - Performance indexes on query columns
- ✅ **Type Safety** - Proper column types and constraints
- ✅ **Mass Assignment Protection** - `$fillable` defined

### Testing

- ✅ **Feature Tests** - Full HTTP lifecycle testing
- ✅ **LazilyRefreshDatabase** - Optimized test performance
- ✅ **Factory Pattern** - Consistent test data generation
- ✅ **Expressive Assertions** - `assertModelExists`, `assertModelMissing`

### Security

- ✅ **Input Validation** - Strict validation rules
- ✅ **SQL Injection Prevention** - Eloquent ORM
- ✅ **Type Casting** - `intval()` for numeric inputs
- ✅ **Whitelist Sorting** - Prevents arbitrary column sorting

## Test Coverage

```
Tests: 15 passed, 15 total
Assertions: 163
Duration: 523ms
```

| Category                           | Tests | Coverage |
| ---------------------------------- | ----- | -------- |
| Index (list, search, filter, sort) | 5     | ✅       |
| Show (success, 404)                | 2     | ✅       |
| Store (create, validation)         | 3     | ✅       |
| Update (success, validation)       | 2     | ✅       |
| Destroy (delete, 404)              | 2     | ✅       |

## API Response Examples

### Success Response (200)

```json
{
  "data": [
    {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@example.com",
      "phone": "08123456789",
      "level": 3,
      "address": "Jakarta",
      "is_active": true,
      "registered_at": "2024-01-15T10:30:00.000000Z",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "links": { ... },
  "total": 50,
  "current_page": 1,
  "last_page": 4
}
```

### Validation Error (422)

```json
{
    "message": "The name field is required.",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email field is required."]
    }
}
```

### Not Found (404)

```json
{
    "message": "No query results for model [App\\Models\\Courier] 999"
}
```

## Development

### Code Style

```bash
# Format code with Pint
vendor/bin/pint --dirty --format agent
```

### Database Seeding

```bash
# Seed with sample data
php artisan db:seed --class=CourierSeeder
```

## Author

**Aldo Ignata Chandra**

- GitHub: [@aldoignatachandra](https://github.com/aldoignatachandra)
- LinkedIn: [in/aldoignatachandra](https://www.linkedin.com/in/aldoignatachandra/)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
