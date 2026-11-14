<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved supplier record. Default display is the supplier name with the
 * supplier id as a secondary field.
 *
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class SupplierResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto         The underlying persistence record
     * @param string|null      $name        Supplier name
     * @param string|null      $supplierId  Supplier id
     * @param array            $labels      Optional per-field label overrides
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $name = null, $supplierId = null, array $labels = array())
    {
        $fields = array(
            'name'        => (string) $name,
            'supplierId'  => (string) $supplierId,
        );

        $defaults = array(
            'name'        => 'Supplier',
            'supplierId'  => 'Supplier Id',
        );

        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    public function toString()
    {
        $name = (string) $this->getField('name');
        $id   = (string) $this->getField('supplierId');
        return $id !== '' ? $name . ' (' . $id . ')' : $name;
    }

    public function toHtml()
    {
        return htmlspecialchars($this->toString(), ENT_QUOTES, 'UTF-8');
    }
}
