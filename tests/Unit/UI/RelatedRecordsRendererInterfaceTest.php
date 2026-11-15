<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\UI;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\UI\RelatedRecordsRendererInterface;
use PHPUnit\Framework\TestCase;

class RelatedRecordsRendererInterfaceTest extends TestCase
{
    public function testInterfaceDeclaresMethod(): void
    {
        $reflection = new \ReflectionClass(RelatedRecordsRendererInterface::class);

        $this->assertTrue($reflection->hasMethod('renderRelatedRecords'));
    }

    public function testMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(RelatedRecordsRendererInterface::class, 'renderRelatedRecords');

        $params = $reflection->getParameters();
        $this->assertCount(4, $params);

        // lhsId has no type hint (mixed)
        $this->assertNull($params[1]->getType());

        // related has array type hint
        $relatedType = $params[2]->getType();
        $this->assertNotNull($relatedType);
        $this->assertSame('array', $relatedType->getName());

        // options has array type hint with default value
        $optionsParam = $params[3];
        $optionsType = $optionsParam->getType();
        $this->assertNotNull($optionsType);
        $this->assertSame('array', $optionsType->getName());
        $this->assertTrue($optionsParam->isDefaultValueAvailable());
        $this->assertSame(array(), $optionsParam->getDefaultValue());

        // No return type declared (mixed/void)
        $this->assertNull($reflection->getReturnType());
    }

    public function testRenderRelatedRecordsReturnsString(): void
    {
        // The interface cannot be instantiated directly; this test verifies
        // the contract shape only. A concrete implementation (in ksf-fa-related-to)
        // provides the actual rendering.
        $this->assertTrue(method_exists(RelatedRecordsRendererInterface::class, 'renderRelatedRecords'));
    }
}
