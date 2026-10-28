<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\UI;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Renderer contract for the SuiteCRM-style related-records display.
 *
 * For a given context record, related records are grouped by rhs type under
 * collapsible (twisty) section headers, e.g. for a Customer: Notes,
 * Opportunities, Contacts, Meetings — each type gets its own table.
 *
 * @since 1.0.0
 */
interface RelatedRecordsRendererInterface
{
    /**
     * Render the related-records section for one context record.
     *
     * Groups related records by rhs type and renders each group under its own
     * collapsible table header.
     *
     * @param string          $lhsType  Context record type key
     * @param string|int      $lhsId    Context record primary key
     * @param RelatedRecordDto[] $related All related records (pre-fetched)
     * @param array           $options  Renderer-specific options (CSS classes, labels, etc.)
     * @return string HTML
     * @since 1.0.0
     */
    public function renderRelatedRecords(
        $lhsType,
        $lhsId,
        array $related,
        array $options = array()
    );
}
