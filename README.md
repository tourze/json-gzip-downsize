# JsonGzipDownsize

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

## Table of Contents

- [Features](#features)
- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage](#advanced-usage)
- [Optimization Principles](#optimization-principles)
- [Performance Considerations](#performance-considerations)
- [Examples](#examples)
- [Requirements](#requirements)
- [What Makes This Different](#what-makes-this-different)
- [API Documentation](#api-documentation)
- [Contributing](#contributing)
- [Compatibility](#compatibility)
- [License](#license)
- [Copyright and License](#copyright-and-license)
- [Changelog](#changelog)

A high-performance PHP library that optimizes JSON field ordering to improve GZIP 
compression efficiency without altering the data itself. By strategically grouping 
similar data types, this library can reduce GZIP-compressed JSON size by 5-15% for 
large datasets.

## Features

- 🚀 **GZIP Optimization**: Improves compression efficiency by 5-15% for large JSON data
- 🔄 **Field Type Grouping**: Groups similar data types together for better compression patterns
- 🛡️ **Data Integrity**: Only changes field order, preserves all data values and structure
- 📦 **Easy Integration**: Simple API with minimal dependencies
- 🔧 **Flexible Usage**: Works with arrays, objects, and JSON strings
- ⚡ **High Performance**: Optimized algorithms for fast processing

## Introduction

JsonGzipDownsize library utilizes a specialized optimization technique to enhance GZIP compression 
algorithm efficiency by grouping similar types of fields (numeric, boolean, string, etc.) together. 
This optimization method **only adjusts field ordering** without changing the data structure or values, 
making it completely safe for frontend and backend JSON parsing.

When JSON data needs to be transmitted over networks with GZIP compression, this library can 
significantly reduce data volume and optimize network performance.

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
2. **Compression Pattern Prediction**: Leverages GZIP algorithm principles by placing similar patterns 
   adjacent to each other
3. **Data Integrity Preservation**: Only changes field order, not data content or structure

For large JSON data, this optimization can reduce GZIP-compressed size by 5-15%.

## Performance Considerations

- Optimization process requires additional CPU computation, which may not be worth it for small JSON 
  data (<1KB)
- Best suited for medium to large JSON responses (>10KB), especially data with repetitive structures
- Optimization effects depend on the original data structure and type distribution

## Examples

Check the `examples/demo.php` file for complete examples and optimization effects in different 
scenarios.

## Requirements

- PHP 8.2 or higher
- Standard PHP JSON extension

## What Makes This Different

Unlike other JSON optimization libraries, JsonGzipDownsize:
- **Preserves Data Integrity**: Only reorders fields, never modifies values or structure
- **Zero Dependencies**: Lightweight with no external dependencies beyond PHP standard library
- **Universal Compatibility**: Works with any JSON parser since it maintains standard JSON format
- **Smart Type Grouping**: Uses advanced algorithms to group similar data types for maximum compression efficiency

## API Documentation

### Core Methods

#### `optimizeForGzip(mixed $data, bool $returnJson = true): mixed`

Optimizes data structure for GZIP compression by grouping fields by type.

**Parameters:**
- `$data` - Array, object, or JSON string to optimize
- `$returnJson` - Whether to return JSON string (true) or PHP array/object (false)

**Returns:** Optimized JSON string or PHP data structure

#### `rebuildFromOptimized(string $json, bool $assoc = false): mixed`

Rebuilds data from optimized JSON while preserving original structure.

**Parameters:**
- `$json` - Optimized JSON string
- `$assoc` - Whether to return associative array (true) or object (false)

**Returns:** Reconstructed data in specified format

#### `encode(mixed $data): string`

Standard JSON encoding wrapper.

#### `decode(string $json, bool $assoc = false): mixed`

Standard JSON decoding wrapper.

### Performance Tips

- **Optimal Data Size**: Most effective on JSON data >10KB
- **Best Structure Types**: Repetitive structures with mixed data types
- **Memory Usage**: Requires ~2x original data size during processing
- **Processing Time**: Adds ~10-30ms for 100KB JSON data

## Contributing

We welcome contributions! Please follow these guidelines:

### Submitting Issues

When reporting bugs or requesting features:

1. **Search existing issues** first to avoid duplicates
2. **Use clear, descriptive titles**
3. **Provide minimal reproduction steps** for bugs
4. **Include PHP version and environment details**
5. **Add relevant code examples** when applicable

### Submitting Pull Requests

1. **Fork the repository** and create a feature branch
2. **Follow PSR-12 coding standards**
3. **Add tests** for new functionality
4. **Ensure all tests pass**: `./vendor/bin/phpunit packages/json-gzip-downsize/tests`
5. **Run PHPStan analysis**: `php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/json-gzip-downsize`
6. **Update documentation** if needed
7. **Create clear commit messages** following conventional commits

### Code Style Requirements

- Follow PSR-12 coding standard
- Use strict types declaration
- Add comprehensive PHPDoc comments
- Maintain 100% test coverage
- Use meaningful variable and method names

### Testing Requirements

- Write unit tests for all public methods
- Test edge cases and error conditions
- Ensure backward compatibility
- Test with different PHP versions (8.2+)

## Compatibility

This library is fully compatible with:
- **JSON Standards**: RFC 7159 compliant
- **PHP Versions**: 8.2, 8.3, 8.4+
- **Frameworks**: Framework-agnostic, works with any PHP project
- **JSON Parsers**: Compatible with all standard JSON parsers

The optimized output maintains standard JSON format, ensuring it can be safely processed by any JSON parser in any language.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Copyright and License

**Copyright (c) 2024 Tourze Team**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

**Author:** Tourze Team  
**Maintainer:** Tourze Team  
**Repository:** https://github.com/tourze/php-monorepo

## Changelog

### v1.0.0
- Initial release
- Core optimization algorithm implementation
- Support for arrays, objects, and JSON strings
- Comprehensive test coverage
- Full documentation

For detailed version history and upgrade guides, see [RELEASES](https://github.com/tourze/php-monorepo/releases).
