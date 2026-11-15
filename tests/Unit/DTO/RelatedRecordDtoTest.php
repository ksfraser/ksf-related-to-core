<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use PHPUnit\Framework\TestCase;

class RelatedRecordDtoTest extends TestCase
{
    public function testConstructionSetsAllProperties(): void
    {
        $dto = new RelatedRecordDto('customer', 'C001', 'opportunity', 'OPP-001', 'regarding');

        $this->assertSame('customer', $dto->lhsType);
        $this->assertSame('C001', $dto->lhsId);
        $this->assertSame('opportunity', $dto->rhsType);
        $this->assertSame('OPP-001', $dto->rhsId);
        $this->assertSame('regarding', $dto->linkRole);
    }

    public function testLhsTypeNormalizedToString(): void
    {
        $dto = new RelatedRecordDto(42, 'C001', 'opportunity', 'OPP-001');

        $this->assertSame('42', $dto->lhsType);
        $this->assertIsString($dto->lhsType);
    }

    public function testRhsTypeNormalizedToString(): void
    {
        $dto = new RelatedRecordDto('customer', 'C001', 7, 'OPP-001');

        $this->assertSame('7', $dto->rhsType);
        $this->assertIsString($dto->rhsType);
    }

    public function testLinkRoleDefaultsToNull(): void
    {
        $dto = new RelatedRecordDto('customer', 'C001', 'opportunity', 'OPP-001');

        $this->assertNull($dto->linkRole);
    }

    public function testToArrayShape(): void
    {
        $dto = new RelatedRecordDto('customer', 'C001', 'opportunity', 'OPP-001', 'regarding');

        $this->assertSame(array(
            'lhs_type' => 'customer',
            'lhs_id' => 'C001',
            'rhs_type' => 'opportunity',
            'rhs_id' => 'OPP-001',
            'link_role' => 'regarding',
        ), $dto->toArray());
    }

    public function testFromArrayReconstructs(): void
    {
        $dto = RelatedRecordDto::fromArray(array(
            'lhs_type' => 'contact',
            'lhs_id' => '7',
            'rhs_type' => 'meeting',
            'rhs_id' => '42',
            'link_role' => 'with',
        ));

        $this->assertSame('contact', $dto->lhsType);
        $this->assertSame('7', $dto->lhsId);
        $this->assertSame('meeting', $dto->rhsType);
        $this->assertSame('42', $dto->rhsId);
        $this->assertSame('with', $dto->linkRole);
    }

    public function testFromArrayWithoutLinkRole(): void
    {
        $dto = RelatedRecordDto::fromArray(array(
            'lhs_type' => 'customer',
            'lhs_id' => 'C001',
            'rhs_type' => 'opportunity',
            'rhs_id' => 'OPP-001',
        ));

        $this->assertNull($dto->linkRole);
    }

    public function testRoundTrip(): void
    {
        $original = new RelatedRecordDto('task', 'T-5', 'project', 'P-10', 'attendee');
        $reconstructed = RelatedRecordDto::fromArray($original->toArray());

        $this->assertSame($original->lhsType, $reconstructed->lhsType);
        $this->assertSame($original->lhsId, $reconstructed->lhsId);
        $this->assertSame($original->rhsType, $reconstructed->rhsType);
        $this->assertSame($original->rhsId, $reconstructed->rhsId);
        $this->assertSame($original->linkRole, $reconstructed->linkRole);
    }
}
