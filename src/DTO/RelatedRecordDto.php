<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

/**
 * A single related-record association: lhs record <-> rhs record.
 *
 * lhs = the record being viewed (the context), rhs = the related record.
 * link_role describes WHY they are related (e.g. regarding, with, document).
 *
 * @since 1.0.0
 */
final class RelatedRecordDto
{
    /** @var string Entity type key (registry key, e.g. customer, contact, meeting) */
    public $lhsType;

    /** @var string|int Primary key of the lhs record */
    public $lhsId;

    /** @var string Entity type key of the related record */
    public $rhsType;

    /** @var string|int Primary key of the related record */
    public $rhsId;

    /** @var string|null Role of the relationship (regarding, with, attendee, document) */
    public $linkRole;

    /**
     * @param string $lhsType
     * @param string|int $lhsId
     * @param string $rhsType
     * @param string|int $rhsId
     * @param string|null $linkRole
     * @since 1.0.0
     */
    public function __construct(
        $lhsType,
        $lhsId,
        $rhsType,
        $rhsId,
        $linkRole = null
    ) {
        $this->lhsType = (string) $lhsType;
        $this->lhsId = $lhsId;
        $this->rhsType = (string) $rhsType;
        $this->rhsId = $rhsId;
        $this->linkRole = $linkRole !== null ? (string) $linkRole : null;
    }

    /**
     * @return array
     * @since 1.0.0
     */
    public function toArray()
    {
        return array(
            'lhs_type'  => $this->lhsType,
            'lhs_id'    => $this->lhsId,
            'rhs_type'  => $this->rhsType,
            'rhs_id'    => $this->rhsId,
            'link_role' => $this->linkRole,
        );
    }

    /**
     * @param array $data
     * @return self
     * @since 1.0.0
     */
    public static function fromArray(array $data)
    {
        return new self(
            $data['lhs_type'],
            $data['lhs_id'],
            $data['rhs_type'],
            $data['rhs_id'],
            isset($data['link_role']) ? $data['link_role'] : null
        );
    }
}
