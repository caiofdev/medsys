# Testing Guide

This document provides comprehensive information on how to run tests in the MedSys application.

## Table of Contents

- [Overview](#overview)
- [Prerequisites](#prerequisites)
- [Test Structure](#test-structure)
- [Running Tests](#running-tests)
- [Writing Tests](#writing-tests)
- [Best Practices](#best-practices)

---

## Overview

MedSys uses PHPUnit for backend testing. The test suite includes:

- **Unit Tests**: Test individual classes and methods in isolation
- **Feature Tests**: Test complete features and HTTP endpoints

---

## Prerequisites

### For Docker Environment

Tests run inside the Docker container, so you need:
- Docker Desktop installed and running
- MedSys containers up and running (`docker-compose up -d`)

### For Local Environment

- PHP 8.2+ installed
- Composer installed
- MySQL database configured
- All dependencies installed (`composer install`)

---

## Test Structure

The test suite is organized as follows:

```
tests/
├── TestCase.php          # Base test case with common setup
├── Feature/              # Feature/integration tests
│   └── ...
└── Unit/                 # Unit tests
    └── ...
```

### TestCase.php

The base `TestCase` class provides common functionality for all tests:
- Database migrations and seeding
- Authentication helpers
- Common assertions

---

## Running Tests

### Docker Environment (Recommended)

#### Run All Tests
```powershell
docker-compose exec app php artisan test
```

#### Run Specific Test File
```powershell
docker-compose exec app php artisan test tests/Feature/ExampleTest.php
```

#### Run Specific Test Method
```powershell
docker-compose exec app php artisan test --filter test_method_name
```

#### Run Tests with Coverage
```powershell
docker-compose exec app php artisan test --coverage
```

#### Run Tests in Parallel
```powershell
docker-compose exec app php artisan test --parallel
```

### Local Environment

#### Run All Tests
```powershell
php artisan test
```

#### Run Specific Test File
```powershell
php artisan test tests/Feature/ExampleTest.php
```

#### Run Specific Test Method
```powershell
php artisan test --filter test_method_name
```

#### Run Tests with Coverage
```powershell
php artisan test --coverage
```

#### Run Tests in Parallel
```powershell
php artisan test --parallel
```

### Using PHPUnit Directly

You can also run tests using PHPUnit directly:

```powershell
# Docker
docker-compose exec app ./vendor/bin/phpunit

# Local
./vendor/bin/phpunit
```

---

## Writing Tests

### Creating a New Test

#### Feature Test
```powershell
php artisan make:test UserControllerTest
```

#### Unit Test
```powershell
php artisan make:test UserModelTest --unit
```

### Basic Test Structure

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
```

### Using Factories

```php
use App\Domain\Models\User;
use App\Domain\Models\Doctor;

public function test_doctor_can_view_dashboard(): void
{
    $user = User::factory()->create();
    $doctor = Doctor::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/doctor/dashboard');

    $response->assertStatus(200);
}
```

### Common Assertions

```php
// HTTP Response Assertions
$response->assertStatus(200);
$response->assertRedirect('/login');
$response->assertJson(['key' => 'value']);
$response->assertViewIs('dashboard');

// Database Assertions
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);

// General Assertions
$this->assertTrue($condition);
$this->assertEquals($expected, $actual);
$this->assertCount(5, $collection);
```

---

## Best Practices

### 1. Use RefreshDatabase Trait

Always use `RefreshDatabase` trait to ensure a clean database state for each test:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
}
```

### 2. Use Factories for Test Data

Always use factories instead of creating models manually:

```php
// Good
$user = User::factory()->create();

// Avoid
$user = new User(['name' => 'Test', 'email' => 'test@test.com']);
$user->save();
```

### 3. Test One Thing at a Time

Each test should verify one specific behavior:

```php
// Good
public function test_user_can_login(): void { /* ... */ }
public function test_user_cannot_login_with_wrong_password(): void { /* ... */ }

// Avoid
public function test_user_authentication(): void { /* tests multiple scenarios */ }
```

### 4. Use Descriptive Test Names

Test names should clearly describe what they're testing:

```php
// Good
public function test_doctor_can_view_own_appointments(): void

// Avoid
public function test_appointments(): void
```

### 5. Arrange-Act-Assert Pattern

Structure your tests following AAA pattern:

```php
public function test_example(): void
{
    // Arrange: Set up test data
    $user = User::factory()->create();

    // Act: Perform the action
    $response = $this->actingAs($user)->get('/dashboard');

    // Assert: Verify the result
    $response->assertStatus(200);
}
```

### 6. Clean Up After Tests

The `RefreshDatabase` trait handles this automatically, but be aware of:
- File uploads (stored in `storage/app/test`)
- External API calls (use mocks)
- Cache data

### 7. Test Edge Cases

Don't just test the happy path:

```php
public function test_user_cannot_create_appointment_in_the_past(): void
public function test_user_cannot_create_appointment_on_weekend(): void
public function test_user_cannot_book_already_booked_slot(): void
```

---

## Configuration

The test configuration is located in `phpunit.xml`:

```xml
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
    </php>
</phpunit>
```

---

## Troubleshooting

### Tests Are Slow

- Use SQLite in-memory database for faster tests
- Run tests in parallel: `php artisan test --parallel`
- Reduce database seeding in tests

### Database Errors

- Ensure migrations are up to date: `php artisan migrate:fresh`
- Check database connection in `phpunit.xml`
- Verify `RefreshDatabase` trait is being used

### Memory Issues

- Increase PHP memory limit: `php -d memory_limit=512M artisan test`
- Reduce test data creation
- Run specific test suites instead of all tests

---

**Author:** Caio  
**Date:** January 5, 2026
