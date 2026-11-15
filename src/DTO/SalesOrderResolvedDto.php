<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved sales order record. Default display is the order number with an
 * optional reference.
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class SalesOrderResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto       The underlying persistence record
     * @param string|null      $orderNo   Sales order number
     * @param string|null      $reference Free-form reference
     * @param array            $labels    Optional per-field label overrides
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $orderNo = null, $reference = null, array $labels = array())
    {
        $fields = array(
            'orderNo'   => (string) $orderNo,
            'reference' => (string) $reference,
        );

        $defaults = array(
            'orderNo'   => 'Sales Order',
            'reference' => 'Reference',
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
        $no  = (string) $this->getField('orderNo');
        $ref = (string) $this->getField('reference');
        return $ref !== '' ? $no . ' / ' . $ref : $no;
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
