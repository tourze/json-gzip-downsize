# JsonGzipDownsize

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

## 目录

- [简介](#简介)
- [特性](#特性)
- [安装](#安装)
- [使用方法](#使用方法)
  - [基本用法](#基本用法)
  - [高级用法](#高级用法)
- [优化原理](#优化原理)
- [性能考虑](#性能考虑)
- [示例](#示例)
- [要求](#要求)
- [与其他库的区别](#与其他库的区别)
- [API 文档](#api-文档)
- [贡献指南](#贡献指南)
- [兼容性](#兼容性)
- [许可证](#许可证)
- [版权和许可](#版权和许可)
- [更新日志](#更新日志)

一个高性能的 PHP 库，通过优化 JSON 字段排序来提高 GZIP 压缩效率，而不改变数据本身。
通过策略性地将相似数据类型分组，此库可以将大型数据集的 GZIP 压缩 JSON 大小减少 5-15%。

## 简介

JsonGzipDownsize 库利用专门的优化技术，通过将类似类型的字段（数字、布尔值、
字符串等）分组在一起来提高 GZIP 压缩算法的效率。这种优化方法**只调整字段顺序**，
不改变数据结构或值，对前端和后端 JSON 解析完全安全。

当 JSON 数据需要通过网络传输并使用 GZIP 压缩时，此库可以显著减少数据量并优化网络性能。

## 特性

- 🚀 **GZIP 优化**：对于大型 JSON 数据，可提高 5-15% 的压缩效率
- 🔄 **字段类型分组**：将相似的数据类型分组以获得更好的压缩模式
- 🛡️ **数据完整性**：仅改变字段顺序，保留所有数据值和结构
- 📦 **易于集成**：简单的 API，最少的依赖
- 🔧 **灵活使用**：适用于数组、对象和 JSON 字符串
- ⚡ **高性能**：优化的算法，快速处理

## 安装

通过 Composer 安装：

```bash
composer require tourze/json-gzip-downsize
```

## 使用方法

### 基本用法

```php
use Tourze\JsonGzipDownsize\JsonGzipDownsize;

// 准备要优化的数据
$data = [
    'name' => 'Product Name',
    'price' => 99.99,
    'id' => 12345,
    'in_stock' => true,
    'description' => 'This is a product description'
];

// 优化数据并返回 JSON 字符串
$optimizedJson = JsonGzipDownsize::optimizeForGzip($data);

// 从优化的 JSON 重建数据（默认返回对象）
$rebuiltObject = JsonGzipDownsize::rebuildFromOptimized($optimizedJson);

// 重建为数组（使用 assoc 参数）
$rebuiltArray = JsonGzipDownsize::rebuildFromOptimized($optimizedJson, true);
```

### 高级用法

#### 优化而不转换为 JSON

```php
// 将第二个参数设为 false，仅优化字段顺序而不转换为 JSON 字符串
$optimizedData = JsonGzipDownsize::optimizeForGzip($data, false);
```

#### 处理 JSON 字符串输入

```php
// 直接处理 JSON 字符串
$jsonString = '{"name":"Product Name","price":99.99,"id":12345}';
$optimizedJson = JsonGzipDownsize::optimizeForGzip($jsonString);
```

#### 处理复杂的嵌套结构

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

// 优化嵌套结构
$optimizedJson = JsonGzipDownsize::optimizeForGzip($nestedData);
```

#### 处理对象

```php
$object = new stdClass();
$object->name = 'Object Example';
$object->id = 555;
$object->active = true;

// 优化对象
$optimizedJson = JsonGzipDownsize::optimizeForGzip($object);
```

## 优化原理

JsonGzipDownsize 使用以下策略来优化 JSON 数据：

1. **字段类型分组**：将相同类型的数据（数字、字符串、布尔值等）放在一起
2. **压缩模式预测**：利用 GZIP 算法原理，将相似的模式放在相邻位置
3. **数据完整性保护**：只改变字段顺序，不改变数据内容或结构

对于大型 JSON 数据，此优化可以减少 5-15% 的 GZIP 压缩大小。

## 性能考虑

- 优化过程需要额外的 CPU 计算，对于小型 JSON 数据（<1KB）可能不值得
- 最适合中大型 JSON 响应（>10KB），特别是具有重复结构的数据
- 优化效果取决于原始数据结构和类型分布

## 示例

查看 `examples/demo.php` 文件以获取完整示例和不同场景下的优化效果。

## 要求

- PHP 8.2 或更高版本
- 标准 PHP JSON 扩展

## 与其他库的区别

与其他 JSON 优化库不同，JsonGzipDownsize：
- **保持数据完整性**：仅重新排序字段，从不修改值或结构
- **零依赖**：轻量级，除 PHP 标准库外无外部依赖
- **通用兼容性**：与任何 JSON 解析器兼容，因为它维护标准 JSON 格式
- **智能类型分组**：使用先进算法将相似数据类型分组以获得最大压缩效率

## API 文档

### 核心方法

#### `optimizeForGzip(mixed $data, bool $returnJson = true): mixed`

通过按类型分组字段来优化数据结构以进行 GZIP 压缩。

**参数：**
- `$data` - 要优化的数组、对象或 JSON 字符串
- `$returnJson` - 是否返回 JSON 字符串 (true) 或 PHP 数组/对象 (false)

**返回值：** 优化的 JSON 字符串或 PHP 数据结构

#### `rebuildFromOptimized(string $json, bool $assoc = false): mixed`

从优化的 JSON 重建数据，同时保留原始结构。

**参数：**
- `$json` - 优化的 JSON 字符串
- `$assoc` - 是否返回关联数组 (true) 或对象 (false)

**返回值：** 指定格式的重建数据

#### `encode(mixed $data): string`

标准 JSON 编码包装器。

#### `decode(string $json, bool $assoc = false): mixed`

标准 JSON 解码包装器。

### 性能提示

- **最佳数据大小**：在 >10KB 的 JSON 数据上最有效
- **最佳结构类型**：具有混合数据类型的重复结构
- **内存使用**：处理期间需要约2倍原始数据大小
- **处理时间**：对于 100KB JSON 数据增加约 10-30ms

## 贡献指南

我们欢迎贡献！请遵循以下指导：

### 提交问题

报告错误或请求功能时：

1. **首先搜索现有问题**以避免重复
2. **使用清晰、描述性的标题**
3. **为错误提供最小重现步骤**
4. **包含 PHP 版本和环境详细信息**
5. **在适用时添加相关代码示例**

### 提交拉取请求

1. **分叉仓库**并创建功能分支
2. **遵循 PSR-12 编码标准**
3. **为新功能添加测试**
4. **确保所有测试通过**：`./vendor/bin/phpunit packages/json-gzip-downsize/tests`
5. **运行 PHPStan 分析**：`php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/json-gzip-downsize`
6. **如有需要更新文档**
7. **创建清晰的提交消息**遵循传统提交规范

### 代码风格要求

- 遵循 PSR-12 编码标准
- 使用严格类型声明
- 添加全面的 PHPDoc 注释
- 保持 100% 测试覆盖率
- 使用有意义的变量和方法名

### 测试要求

- 为所有公共方法编写单元测试
- 测试边界情况和错误条件
- 确保向后兼容性
- 使用不同 PHP 版本测试 (8.2+)

## 兼容性

此库完全兼容：
- **JSON 标准**：符合 RFC 7159
- **PHP 版本**：8.2, 8.3, 8.4+
- **框架**：框架无关，适用于任何 PHP 项目
- **JSON 解析器**：与所有标准 JSON 解析器兼容

优化的输出保持标准 JSON 格式，确保可以被任何语言的任何 JSON 解析器安全处理。

## 许可证

本项目采用 MIT 许可证 - 详情请见 [LICENSE](LICENSE) 文件。

## 版权和许可

**版权所有 (c) 2024 Tourze Team**

本项目采用 MIT 许可证 - 详情请见 [LICENSE](LICENSE) 文件。

**作者：** Tourze Team  
**维护者：** Tourze Team  
**仓库：** https://github.com/tourze/php-monorepo

## 更新日志

### v1.0.0
- 初始发布
- 核心优化算法实现
- 支持数组、对象和 JSON 字符串
- 全面测试覆盖
- 完整文档

详细版本历史和升级指南，请查看 [RELEASES](https://github.com/tourze/php-monorepo/releases)。
