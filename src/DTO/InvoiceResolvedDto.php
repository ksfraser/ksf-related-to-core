<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved invoice record. Default display is the invoice number (reference)
 * with an optional reference field. Invoice number is stored in the
 * 'invoiceNo' field; 'reference' is the free-form reference.
 *
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class InvoiceResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto      The underlying persistence record
     * @param string|null      $invoiceNo Invoice number
     * @param string|null      $reference Free-form reference
     * @param array            $labels   Optional per-field label overrides
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $invoiceNo = null, $reference = null, array $labels = array())
    {
        $fields = array(
            'invoiceNo' => (string) $invoiceNo,
            'reference' => (string) $reference,
        );

        $defaults = array(
            'invoiceNo' => 'Invoice No',
            'reference' => 'Reference',
        );

        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    public function toString()
    {
        $inv = (string) $this->getField('invoiceNo');
        $ref = (string) $this->getField('reference');
        $parts = array();
        if ($inv !== '') {
            $parts[] = $inv;
        }
        if ($ref !== '') {
            $parts[] = $ref;
        }
        return implode(' / ', $parts);
    }

    public function toHtml()
    {
        return htmlspecialchars($this->toString(), ENT_QUOTES, 'UTF-8');
    }
}
