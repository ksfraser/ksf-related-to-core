<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\DTO;

use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use PHPUnit\Framework\TestCase;

class RelatedQueryDtoTest extends TestCase
{
    public function testConstructionSetsAllProperties(): void
    {
        $dto = new RelatedQueryDto('customer', 'C001', 'opportunity', 10, 0);

        $this->assertSame('customer', $dto->lhsType);
        $this->assertSame('C001', $dto->lhsId);
        $this->assertSame('opportunity', $dto->rhsType);
        $this->assertSame(10, $dto->limit);
        $this->assertSame(0, $dto->offset);
    }

    public function testLhsTypeNormalizedToString(): void
    {
        $dto = new RelatedQueryDto(42, 'C001');

        $this->assertSame('42', $dto->lhsType);
        $this->assertIsString($dto->lhsType);
    }

    public function testRhsTypeNullWhenOmitted(): void
    {
        $dto = new RelatedQueryDto('customer', 'C001');

        $this->assertNull($dto->rhsType);
    }

    public function testLimitAndOffsetDefaultToNull(): void
    {
        $dto = new RelatedQueryDto('customer', 'C001');

        $this->assertNull($dto->limit);
        $this->assertNull($dto->offset);
    }

    public function testToArrayShape(): void
    {
        $dto = new RelatedQueryDto('customer', 'C001', 'opportunity', 10, 0);

        $this->assertSame(array(
            'lhs_type' => 'customer',
            'lhs_id' => 'C001',
            'rhs_type' => 'opportunity',
            'limit' => 10,
            'offset' => 0,
        ), $dto->toArray());
    }

    public function testToArrayWithoutOptionalFields(): void
    {
        $dto = new RelatedQueryDto('customer', 'C001');

        $this->assertSame(array(
            'lhs_type' => 'customer',
            'lhs_id' => 'C001',
            'rhs_type' => null,
            'limit' => null,
            'offset' => null,
        ), $dto->toArray());
    }

    public function testFromArrayReconstructs(): void
    {
        $dto = RelatedQueryDto::fromArray(array(
            'lhs_type' => 'contact',
            'lhs_id' => '7',
            'rhs_type' => 'meeting',
            'limit' => 5,
            'offset' => 1,
        ));

        $this->assertSame('contact', $dto->lhsType);
        $this->assertSame('7', $dto->lhsId);
        $this->assertSame('meeting', $dto->rhsType);
        $this->assertSame(5, $dto->limit);
        $this->assertSame(1, $dto->offset);
    }

    public function testFromArrayWithoutOptionalFields(): void
    {
        $dto = RelatedQueryDto::fromArray(array(
            'lhs_type' => 'customer',
            'lhs_id' => 'C001',
        ));

        $this->assertNull($dto->rhsType);
        $this->assertNull($dto->limit);
        $this->assertNull($dto->offset);
    }
}
