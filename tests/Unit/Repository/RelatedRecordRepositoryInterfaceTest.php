<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\Repository;

use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\Repository\RelatedRecordRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RelatedRecordRepositoryInterfaceTest extends TestCase
{
    public function testInterfaceDeclaresAllMethods(): void
    {
        $reflection = new \ReflectionClass(RelatedRecordRepositoryInterface::class);

        $this->assertTrue($reflection->hasMethod('relate'));
        $this->assertTrue($reflection->hasMethod('unrelate'));
        $this->assertTrue($reflection->hasMethod('relatedTo'));
        $this->assertTrue($reflection->hasMethod('relatedBy'));
        $this->assertTrue($reflection->hasMethod('isRelated'));
    }

    public function testRelateMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(RelatedRecordRepositoryInterface::class, 'relate');

        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('ksfraser\\RelatedTo\\DTO\\RelatedRecordDto', $params[0]->getType()->getName());
        $this->assertSame('bool', $reflection->getReturnType()->getName());
    }

    public function testUnrelateMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(RelatedRecordRepositoryInterface::class, 'unrelate');

        $params = $reflection->getParameters();
        $this->assertCount(5, $params);
        $this->assertSame('bool', $reflection->getReturnType()->getName());
    }

    public function testRelatedToMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(RelatedRecordRepositoryInterface::class, 'relatedTo');

        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('ksfraser\\RelatedTo\\DTO\\RelatedQueryDto', $params[0]->getType()->getName());
        $returnType = $reflection->getReturnType();
        $this->assertSame('array', $returnType->getName());
    }

    public function testRelatedByMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(RelatedRecordRepositoryInterface::class, 'relatedBy');

        $params = $reflection->getParameters();
        $this->assertCount(3, $params);
        $returnType = $reflection->getReturnType();
        $this->assertSame('array', $returnType->getName());
    }

    public function testIsRelatedMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(RelatedRecordRepositoryInterface::class, 'isRelated');

        $params = $reflection->getParameters();
        $this->assertCount(5, $params);
        $this->assertSame('bool', $reflection->getReturnType()->getName());
    }
}
