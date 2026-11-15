<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved customer record. Default display is the customer name with the
 * debtor number as a secondary field.
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class CustomerResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto     The underlying persistence record
     * @param string|null      $name    Customer name
     * @param string|null      $debtorNo Debtor number
     * @param array            $labels  Optional per-field label overrides
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $name = null, $debtorNo = null, array $labels = array())
    {
        $fields = array(
            'name'     => (string) $name,
            'debtorNo' => (string) $debtorNo,
        );

        $defaults = array(
            'name'     => 'Customer',
            'debtorNo' => 'Debtor No',
        );

        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    /**
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toString()
    {
        $name = (string) $this->getField('name');
        $no   = (string) $this->getField('debtorNo');
        return $no !== '' ? $name . ' (' . $no . ')' : $name;
    }

    /**
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toHtml()
    {
        return htmlspecialchars($this->toString(), ENT_QUOTES, 'UTF-8');
    }
}
