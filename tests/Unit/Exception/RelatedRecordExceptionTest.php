<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\Exception;

use ksfraser\RelatedTo\Exception\RelatedRecordException;
use ksfraser\RelatedTo\Exception\RelatedRecordPersistenceException;
use ksfraser\RelatedTo\Exception\UnknownRelatedTypeException;
use PHPUnit\Framework\TestCase;

class RelatedRecordExceptionTest extends TestCase
{
    public function testRelatedRecordExceptionExtendsRuntimeException(): void
    {
        $this->assertTrue(is_subclass_of(RelatedRecordException::class, \RuntimeException::class));
    }

    public function testUnknownRelatedTypeExceptionExtendsBase(): void
    {
        $this->assertTrue(is_subclass_of(UnknownRelatedTypeException::class, RelatedRecordException::class));
    }

    public function testRelatedRecordPersistenceExceptionExtendsBase(): void
    {
        $this->assertTrue(is_subclass_of(RelatedRecordPersistenceException::class, RelatedRecordException::class));
    }

    public function testExceptionsCanBeInstantiatedAndThrown(): void
    {
        $base = new RelatedRecordException('base error');
        $unknown = new UnknownRelatedTypeException('unknown type');
        $persistence = new RelatedRecordPersistenceException('persistence error');

        $this->assertSame('base error', $base->getMessage());
        $this->assertSame('unknown type', $unknown->getMessage());
        $this->assertSame('persistence error', $persistence->getMessage());

        $this->expectException(UnknownRelatedTypeException::class);
        throw new UnknownRelatedTypeException('test throw');
    }
}
