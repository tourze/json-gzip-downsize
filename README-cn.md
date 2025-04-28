# JsonGzipDownsize

一个优化JSON字段排序以提高GZIP压缩效率的PHP库，不会改变数据本身。

## 简介

JsonGzipDownsize库利用专门的优化技术，通过将相似类型的字段（数字、布尔值、字符串等）分组在一起，提高GZIP压缩算法的效率。这种优化方法**仅调整字段排序**，不改变数据结构或值，使其对前端和后端的JSON解析完全安全。

当JSON数据需要通过GZIP压缩在网络上传输时，该库可以显著减少数据量并优化网络性能。

## 安装

通过Composer安装：

```bash
composer require tourze/json-gzip-downsize
```

## 使用方法

### 基本用法

```php
use Tourze\JsonGzipDownsize\JsonGzipDownsize;

// 准备要优化的数据
$data = [
    'name' => '产品名称',
    'price' => 99.99,
    'id' => 12345,
    'in_stock' => true,
    'description' => '这是产品描述'
];

// 优化数据并返回JSON字符串
$optimizedJson = JsonGzipDownsize::optimizeForGzip($data);

// 从优化后的JSON重建数据（默认返回对象）
$rebuiltObject = JsonGzipDownsize::rebuildFromOptimized($optimizedJson);

// 重建为数组（使用assoc参数）
$rebuiltArray = JsonGzipDownsize::rebuildFromOptimized($optimizedJson, true);
```

### 高级用法

#### 优化但不转换为JSON

```php
// 设置第二个参数为false，只优化字段顺序而不转换为JSON字符串
$optimizedData = JsonGzipDownsize::optimizeForGzip($data, false);
```

#### 处理JSON字符串输入

```php
// 直接处理JSON字符串
$jsonString = '{"name":"产品名称","price":99.99,"id":12345}';
$optimizedJson = JsonGzipDownsize::optimizeForGzip($jsonString);
```

#### 处理复杂嵌套结构

```php
$nestedData = [
    'user' => [
        'id' => 1001,
        'name' => '张三',
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
$object->name = '对象示例';
$object->id = 555;
$object->active = true;

// 优化对象
$optimizedJson = JsonGzipDownsize::optimizeForGzip($object);
```

## 优化原理

JsonGzipDownsize使用以下策略来优化JSON数据：

1. **字段类型分组**：将相同类型的数据（数字、字符串、布尔值等）放在一起
2. **压缩模式预测**：利用GZIP算法原理，将相似模式放在相邻位置
3. **数据完整性保护**：只更改字段顺序，不改变数据内容或结构

对于大型JSON数据，这种优化可以减少GZIP压缩后的大小5-15%。

## 性能考虑

- 优化过程需要额外的CPU计算，对于小型JSON数据（<1KB）可能不值得
- 最适合中大型JSON响应（>10KB），特别是具有重复结构的数据
- 优化效果取决于原始数据结构和类型分布

## 示例

查看`examples/demo.php`文件以获取完整示例和不同场景下的优化效果。

## 要求

- PHP 8.0或更高版本
- 标准PHP JSON扩展

## 兼容性

该库与标准JSON格式完全兼容，优化后的数据可以被任何标准JSON解析器正确处理。由于它只调整字段顺序而不影响数据本身，可以安全使用。

## 许可证

MIT
