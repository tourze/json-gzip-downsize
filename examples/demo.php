<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Tourze\JsonGzipDownsize\JsonGzipDownsize;

// 设置标题函数
function printTitle(string $title): void
{
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
    echo $title . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
}

// 演示函数
function demo(array $data, string $demoName): void
{
    printTitle($demoName);

    echo '原始数据：' . PHP_EOL;
    print_r($data);

    // 1. 将数据优化为JSON
    $optimizedJson = JsonGzipDownsize::optimizeForGzip($data);
    echo PHP_EOL . '优化后的JSON：' . PHP_EOL;
    echo $optimizedJson . PHP_EOL;

    // 2. 计算压缩效率
    $normal = gzencode(json_encode($data));
    $optimized = gzencode($optimizedJson);

    echo PHP_EOL . '压缩效率比较：' . PHP_EOL;
    echo '标准JSON压缩大小: ' . strlen($normal) . ' 字节' . PHP_EOL;
    echo '优化后JSON压缩大小: ' . strlen($optimized) . ' 字节' . PHP_EOL;

    if (strlen($normal) > 0) {
        $improvement = 100 * (1 - strlen($optimized) / strlen($normal));
        echo '改进比例: ' . number_format($improvement, 2) . '%' . PHP_EOL;
    }

    // 3. 重建数据
    echo PHP_EOL . '重建为对象：' . PHP_EOL;
    $rebuiltObj = JsonGzipDownsize::rebuildFromOptimized($optimizedJson);
    print_r($rebuiltObj);

    echo PHP_EOL . '重建为数组：' . PHP_EOL;
    $rebuiltArray = JsonGzipDownsize::rebuildFromOptimized($optimizedJson, true);
    print_r($rebuiltArray);
}

// 示例1：简单对象
printTitle('示例1：简单对象');

$simpleData = [
    'name' => '产品名称',
    'price' => 99.99,
    'id' => 12345,
    'in_stock' => true,
    'description' => '这是一个产品描述',
];

demo($simpleData, '简单对象优化');

// 示例2：嵌套结构
printTitle('示例2：嵌套结构');

$nestedData = [
    'user' => [
        'id' => 1001,
        'name' => '张三',
        'email' => 'zhangsan@example.com',
        'active' => true,
    ],
    'orders' => [
        [
            'id' => 5001,
            'total' => 199.99,
            'products' => [
                'id' => 101,
                'name' => '商品1',
                'quantity' => 2,
            ],
            'paid' => false,
        ],
        [
            'id' => 5002,
            'total' => 299.99,
            'products' => [
                'id' => 102,
                'name' => '商品2',
                'quantity' => 1,
            ],
            'paid' => true,
        ],
    ],
    'notification_settings' => [
        'email' => true,
        'sms' => false,
        'push' => true,
    ],
];

demo($nestedData, '嵌套结构优化');

// 示例3：直接处理JSON字符串
printTitle('示例3：直接处理JSON字符串');

$jsonString = json_encode([
    'tags' => ['推荐', '热门', '新品'],
    'id' => 999,
    'status' => 'active',
    'views' => 10245,
    'hidden' => false,
]);

echo '原始JSON字符串：' . PHP_EOL;
echo $jsonString . PHP_EOL;

$optimizedJson = JsonGzipDownsize::optimizeForGzip($jsonString);

echo PHP_EOL . '优化后JSON字符串：' . PHP_EOL;
echo $optimizedJson . PHP_EOL;

// 示例4：保持数据为PHP变量（不转换为JSON）
printTitle('示例4：优化后保持为PHP变量');

$data = [
    'name' => '测试产品',
    'price' => 88.88,
    'id' => 777,
    'features' => ['特性1', '特性2', '特性3'],
    'available' => true,
];

echo '原始数据：' . PHP_EOL;
print_r($data);

$optimizedData = JsonGzipDownsize::optimizeForGzip($data, false);

echo PHP_EOL . '优化后数据（仍为PHP变量）：' . PHP_EOL;
print_r($optimizedData);

// 示例5：对象处理
printTitle('示例5：对象处理');

$object = new stdClass();
$object->name = '对象示例';
$object->id = 555;
$object->active = true;
$object->data = ['a', 'b', 'c'];

echo '原始对象：' . PHP_EOL;
print_r($object);

$optimizedObject = JsonGzipDownsize::optimizeForGzip($object, false);

echo PHP_EOL . '优化后对象：' . PHP_EOL;
print_r($optimizedObject);

// 结束
printTitle('演示结束');
echo '以上示例展示了JsonGzipDownsize的基本用法和不同场景下的效果。' . PHP_EOL;
echo '注意：实际压缩效果可能根据数据大小和结构有所不同。' . PHP_EOL;
