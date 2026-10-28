<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

/**
 * Search criteria for "what is related to this record?"
 *
 * @since 1.0.0
 */
final class RelatedQueryDto
{
    /** @var string Entity type key of the context record */
    public $lhsType;

    /** @var string|int Primary key of the context record */
    public $lhsId;

    /** @var string|null Filter by related entity type */
    public $rhsType;

    /** @var int|null Maximum rows to return */
    public $limit;

    /** @var int|null Offset for paging */
    public $offset;

    /**
     * @param string $lhsType
     * @param string|int $lhsId
     * @param string|null $rhsType
     * @param int|null $limit
     * @param int|null $offset
     * @since 1.0.0
     */
    public function __construct(
        $lhsType,
        $lhsId,
        $rhsType = null,
        $limit = null,
        $offset = null
    ) {
        $this->lhsType = (string) $lhsType;
        $this->lhsId = $lhsId;
        $this->rhsType = $rhsType !== null ? (string) $rhsType : null;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * @return array
     * @since 1.0.0
     */
    public function toArray()
    {
        return array(
            'lhs_type' => $this->lhsType,
            'lhs_id'   => $this->lhsId,
            'rhs_type' => $this->rhsType,
            'limit'    => $this->limit,
            'offset'   => $this->offset,
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
            isset($data['rhs_type']) ? $data['rhs_type'] : null,
            isset($data['limit']) ? $data['limit'] : null,
            isset($data['offset']) ? $data['offset'] : null
        );
    }
}
