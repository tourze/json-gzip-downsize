# JsonGzipDownsize

A PHP library that optimizes JSON field ordering to improve GZIP compression efficiency without altering the data itself.

## Introduction

JsonGzipDownsize library utilizes a specialized optimization technique to enhance GZIP compression algorithm efficiency by grouping similar types of fields (numeric, boolean, string, etc.) together. This optimization method **only adjusts field ordering** without changing the data structure or values, making it completely safe for frontend and backend JSON parsing.

When JSON data needs to be transmitted over networks with GZIP compression, this library can significantly reduce data volume and optimize network performance.

## Installation

Install via Composer:

```bash
composer require tourze/json-gzip-downsize
```

## Usage

### Basic Usage

```php
use Tourze\JsonGzipDownsize\JsonGzipDownsize;

// Prepare the data to optimize
$data = [
    'name' => 'Product Name',
    'price' => 99.99,
    'id' => 12345,
    'in_stock' => true,
    'description' => 'This is a product description'
];

// Optimize data and return JSON string
$optimizedJson = JsonGzipDownsize::optimizeForGzip($data);

// Rebuild from optimized JSON (returns object by default)
$rebuiltObject = JsonGzipDownsize::rebuildFromOptimized($optimizedJson);

// Rebuild as array (using assoc parameter)
$rebuiltArray = JsonGzipDownsize::rebuildFromOptimized($optimizedJson, true);
```

### Advanced Usage

#### Optimize Without Converting to JSON

```php
// Set the second parameter to false to only optimize field order without converting to JSON string
$optimizedData = JsonGzipDownsize::optimizeForGzip($data, false);
```

#### Handle JSON String Input

```php
// Directly process a JSON string
$jsonString = '{"name":"Product Name","price":99.99,"id":12345}';
$optimizedJson = JsonGzipDownsize::optimizeForGzip($jsonString);
```

#### Process Complex Nested Structures

```php
$nestedData = [
    'user' => [
        'id' => 1001,
        'name' => 'John Doe',
        'active' => true
    ],
    'orders' => [
        [
            'id' => 5001,
            'total' => 199.99,
            'paid' => false
        ]
    ]
];

// Optimize nested structure
$optimizedJson = JsonGzipDownsize::optimizeForGzip($nestedData);
```

#### Handle Objects

```php
$object = new stdClass();
$object->name = 'Object Example';
$object->id = 555;
$object->active = true;

// Optimize object
$optimizedJson = JsonGzipDownsize::optimizeForGzip($object);
```

## Optimization Principles

JsonGzipDownsize uses the following strategies to optimize JSON data:

1. **Field Type Grouping**: Places data of the same type (numbers, strings, booleans, etc.) together
2. **Compression Pattern Prediction**: Leverages GZIP algorithm principles by placing similar patterns adjacent to each other
3. **Data Integrity Preservation**: Only changes field order, not data content or structure

For large JSON data, this optimization can reduce GZIP-compressed size by 5-15%.

## Performance Considerations

- Optimization process requires additional CPU computation, which may not be worth it for small JSON data (<1KB)
- Best suited for medium to large JSON responses (>10KB), especially data with repetitive structures
- Optimization effects depend on the original data structure and type distribution

## Examples

Check the `examples/demo.php` file for complete examples and optimization effects in different scenarios.

## Requirements

- PHP 8.0 or higher
- Standard PHP JSON extension

## Compatibility

This library is fully compatible with standard JSON format, and optimized data can be correctly processed by any standard JSON parser. Since it only adjusts field order without affecting the data itself, it can be used safely.

## License

MIT
