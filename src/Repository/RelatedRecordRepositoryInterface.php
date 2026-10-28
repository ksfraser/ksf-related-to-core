<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\Repository;

use ksfraser\RelatedTo\DTO\RelatedQueryDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Repository contract for persisting and fetching related-record associations.
 *
 * Implementations are platform-specific (FA native db_*, PDO, WordPress, etc.)
 * and are injected wherever related-record storage is needed.
 *
 * @since 1.0.0
 */
interface RelatedRecordRepositoryInterface
{
    /**
     * Associate two records.
     *
     * @param RelatedRecordDto $dto
     * @return bool True when the association was created (or already existed)
     * @throws \ksfraser\RelatedTo\Exception\RelatedRecordPersistenceException
     * @since 1.0.0
     */
    public function relate(RelatedRecordDto $dto): bool;

    /**
     * Remove an association between two records.
     *
     * @param string $lhsType
     * @param string|int $lhsId
     * @param string $rhsType
     * @param string|int $rhsId
     * @param string|null $linkRole
     * @return bool True when a row was removed
     * @throws \ksfraser\RelatedTo\Exception\RelatedRecordPersistenceException
     * @since 1.0.0
     */
    public function unrelate($lhsType, $lhsId, $rhsType, $rhsId, $linkRole = null): bool;

    /**
     * Fetch all records related to one context record, optionally filtered by rhs type.
     *
     * @param RelatedQueryDto $query
     * @return RelatedRecordDto[]
     * @throws \ksfraser\RelatedTo\Exception\RelatedRecordPersistenceException
     * @since 1.0.0
     */
    public function relatedTo(RelatedQueryDto $query): array;

    /**
     * Fetch all records that relate to one target record (reverse lookup).
     *
     * @param string $rhsType
     * @param string|int $rhsId
     * @param string|null $lhsType Filter by context type
     * @return RelatedRecordDto[]
     * @throws \ksfraser\RelatedTo\Exception\RelatedRecordPersistenceException
     * @since 1.0.0
     */
    public function relatedBy($rhsType, $rhsId, $lhsType = null): array;

    /**
     * Check whether an association exists.
     *
     * @param string $lhsType
     * @param string|int $lhsId
     * @param string $rhsType
     * @param string|int $rhsId
     * @param string|null $linkRole
     * @return bool
     * @throws \ksfraser\RelatedTo\Exception\RelatedRecordPersistenceException
     * @since 1.0.0
     */
    public function isRelated($lhsType, $lhsId, $rhsType, $rhsId, $linkRole = null): bool;
}
