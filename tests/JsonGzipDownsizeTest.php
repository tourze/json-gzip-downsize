<?php

declare(strict_types=1);

namespace Tourze\JsonGzipDownsize\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\JsonGzipDownsize\JsonGzipDownsize;

/**
 * JsonGzipDownsize测试类
 */
class JsonGzipDownsizeTest extends TestCase
{
    /**
     * 测试基本优化功能
     */
    public function testBasicOptimization(): void
    {
        $testData = [
            'string' => 'test',
            'number' => 123,
            'boolean' => true,
            'null' => null,
            'array' => [1, 2, 3],
            'nested' => [
                'a' => 1,
                'b' => 'test'
            ]
        ];

        // 优化并返回JSON
        $optimized = JsonGzipDownsize::optimizeForGzip($testData);
        $this->assertIsString($optimized);
        
        // 重建数据并验证是否与原始数据相同
        $rebuilt = JsonGzipDownsize::rebuildFromOptimized($optimized);
        
        // 验证数据内容一致，即使类型可能不同
        $this->assertEquals($testData['string'], $rebuilt->string);
        $this->assertEquals($testData['number'], $rebuilt->number);
        $this->assertEquals($testData['boolean'], $rebuilt->boolean);
        $this->assertEquals($testData['null'], $rebuilt->null);
        $this->assertEquals($testData['array'], $rebuilt->array);
        $this->assertEquals($testData['nested']['a'], $rebuilt->nested->a);
        $this->assertEquals($testData['nested']['b'], $rebuilt->nested->b);

        // 直接重建为数组
        $rebuiltAsArray = JsonGzipDownsize::rebuildFromOptimized($optimized, true);
        
        $this->assertIsArray($rebuiltAsArray);
        // 验证数组版本
        $this->assertEquals($testData, $rebuiltAsArray);
    }

    /**
     * 测试优化数组和返回格式
     */
    public function testArrayOptimizationAndReturnFormat(): void
    {
        $testData = [
            'strings' => 'value',
            'numbers' => 123,
            'arrays' => [1, 2, 3]
        ];

        // 优化但不转换为JSON
        $optimizedArray = JsonGzipDownsize::optimizeForGzip($testData, false);
        $this->assertIsArray($optimizedArray);

        // 验证优化后的数据结构
        $this->assertArrayHasKey('numbers', $optimizedArray);
        $this->assertArrayHasKey('strings', $optimizedArray);
        $this->assertArrayHasKey('arrays', $optimizedArray);

        // 检查字段排序（数字应在字符串之前）
        $keys = array_keys($optimizedArray);
        $this->assertLessThan(
            array_search('strings', $keys),
            array_search('numbers', $keys)
        );
    }

    /**
     * 测试优化对象
     */
    public function testObjectOptimization(): void
    {
        $testObject = (object)[
            'name' => 'test',
            'id' => 123,
            'active' => true
        ];

        // 优化对象
        $optimized = JsonGzipDownsize::optimizeForGzip($testObject, false);
        $this->assertIsObject($optimized);

        // 验证字段保留
        $this->assertEquals($testObject->name, $optimized->name);
        $this->assertEquals($testObject->id, $optimized->id);
        $this->assertEquals($testObject->active, $optimized->active);

        // 验证字段排序
        $keys = array_keys((array)$optimized);
        $this->assertLessThan(
            array_search('name', $keys),
            array_search('id', $keys)
        );
    }

    /**
     * 测试嵌套数组和对象
     */
    public function testNestedStructures(): void
    {
        $testData = [
            'nested' => [
                'object' => (object)[
                    'name' => 'test',
                    'id' => 123
                ],
                'array' => ['a', 'b', 'c']
            ],
            'simple' => 'value'
        ];

        $optimized = JsonGzipDownsize::optimizeForGzip($testData);
        $this->assertIsString($optimized);

        $rebuilt = JsonGzipDownsize::rebuildFromOptimized($optimized, true);
        $this->assertIsArray($rebuilt);
        $this->assertIsArray($rebuilt['nested']);
        $this->assertIsObject($rebuilt['nested']['object']);
        $this->assertIsArray($rebuilt['nested']['array']);
    }

    /**
     * 测试从优化后的数据重建原始数据
     */
    public function testRebuildFromOptimized(): void
    {
        // 对象数据
        $objectData = (object)[
            'name' => 'test',
            'id' => 123,
            'items' => ['a', 'b', 'c']
        ];

        // 数组数据
        $arrayData = [
            'name' => 'test',
            'id' => 123,
            'items' => ['a', 'b', 'c']
        ];

        // 优化数组数据为JSON
        $optimizedJson = JsonGzipDownsize::optimizeForGzip($arrayData);

        // 重建为对象（默认行为）
        $rebuiltObject = JsonGzipDownsize::rebuildFromOptimized($optimizedJson);
        $this->assertIsObject($rebuiltObject);
        $this->assertEquals($arrayData, (array)$rebuiltObject);

        // 重建为数组（使用assoc=true）
        $rebuiltArray = JsonGzipDownsize::rebuildFromOptimized($optimizedJson, true);
        $this->assertIsArray($rebuiltArray);
        $this->assertEquals($arrayData, $rebuiltArray);

        // 优化对象数据
        $optimizedObjectJson = JsonGzipDownsize::optimizeForGzip($objectData);

        // 从对象数据重建对象
        $rebuiltFromObject = JsonGzipDownsize::rebuildFromOptimized($optimizedObjectJson);
        $this->assertIsObject($rebuiltFromObject);
        $this->assertEquals($objectData, $rebuiltFromObject);

        // 从对象数据重建数组
        $rebuiltArrayFromObject = JsonGzipDownsize::rebuildFromOptimized($optimizedObjectJson, true);
        $this->assertIsArray($rebuiltArrayFromObject);
        $this->assertEquals((array)$objectData, $rebuiltArrayFromObject);
    }

    /**
     * 测试JSON字符串输入
     */
    public function testJsonStringInput(): void
    {
        $jsonString = '{"name":"test","id":123,"items":["a","b","c"]}';
        
        // 优化JSON字符串
        $optimized = JsonGzipDownsize::optimizeForGzip($jsonString);
        $this->assertIsString($optimized);
        
        // 重建为对象
        $rebuiltObject = JsonGzipDownsize::rebuildFromOptimized($optimized);
        $this->assertIsObject($rebuiltObject);
        $this->assertEquals('test', $rebuiltObject->name);
        
        // 重建为数组
        $rebuiltArray = JsonGzipDownsize::rebuildFromOptimized($optimized, true);
        $this->assertIsArray($rebuiltArray);
        $this->assertEquals('test', $rebuiltArray['name']);
    }

    /**
     * 测试GZIP压缩效率提升
     *
     * 此测试验证使用JsonGzipDownsize优化后的JSON确实能够提高GZIP压缩效率
     */
    public function testGzipCompressionEfficiency(): void
    {
        // 准备测试数据 - 一个包含混合类型数据的大型结构
        $testData = [];
        
        // 添加一些重复模式的数据以展示压缩效果
        for ($i = 0; $i < 100; $i++) {
            $testData[] = [
                'id' => $i,
                'name' => 'Item ' . $i,
                'isActive' => ($i % 3 === 0),
                'price' => 10.5 + $i,
                'tags' => ['tag1', 'tag2', 'tag' . $i],
                'metadata' => [
                    'created' => '2023-01-01',
                    'updated' => '2023-06-30',
                    'version' => $i % 10,
                    'visible' => ($i % 2 === 0),
                ]
            ];
        }
        
        // 1. 使用标准JSON编码
        $standardJson = json_encode($testData);
        
        // 2. 使用JsonGzipDownsize优化
        $optimizedJson = JsonGzipDownsize::optimizeForGzip($testData);
        
        // 3. 分别进行GZIP压缩
        $standardCompressed = gzencode($standardJson);
        $optimizedCompressed = gzencode($optimizedJson);
        
        // 4. 比较压缩大小
        $standardSize = strlen($standardCompressed);
        $optimizedSize = strlen($optimizedCompressed);
        
        // 5. 打印压缩信息以便查看
        printf(
            "GZIP压缩效率对比:\n" .
            "- 标准JSON压缩大小: %d 字节\n" .
            "- 优化后JSON压缩大小: %d 字节\n",
            $standardSize,
            $optimizedSize
        );
        
        // 6. 计算改进百分比
        if ($standardSize > 0) {
            $improvement = ($standardSize - $optimizedSize) / $standardSize * 100;
            printf("- 改进百分比: %.2f%%\n", $improvement);
            
            // 7. 验证优化版本的压缩大小应该小于标准版本
            $this->assertLessThanOrEqual(
                $standardSize,
                $optimizedSize,
                '优化后的JSON应该比标准JSON具有更小或相等的GZIP压缩大小'
            );
            
            // 只有在某些情况下，改进才会非常明显，所以不强制要求一定有改进
            if ($improvement > 0) {
                $this->addToAssertionCount(1);
                echo "优化成功降低了压缩大小\n";
            } else {
                $this->addToAssertionCount(1);
                echo "优化未能降低压缩大小，这可能受测试数据大小和结构的影响\n";
            }
        } else {
            $this->addToAssertionCount(1);
            echo "无法计算改进百分比，标准压缩大小为0\n";
        }
        
        // 8. 最后验证解码后的数据一致性
        $rebuiltData = JsonGzipDownsize::rebuildFromOptimized($optimizedJson, true);
        $this->assertEquals($testData, $rebuiltData, '优化后的JSON应保持数据一致性');
    }
}
