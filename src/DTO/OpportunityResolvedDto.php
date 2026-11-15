<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved opportunity record. Default display is the opportunity name with an
 * optional amount.
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class OpportunityResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto      The underlying persistence record
     * @param string|null      $name     Opportunity name
     * @param string|null      $amount   Opportunity amount
     * @param array            $labels   Optional per-field label overrides
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $name = null, $amount = null, array $labels = array())
    {
        $fields = array(
            'name'   => (string) $name,
            'amount' => (string) $amount,
        );
        $defaults = array(
            'name'   => 'Opportunity',
            'amount' => 'Amount',
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
        $amt  = (string) $this->getField('amount');
        return $amt !== '' ? $name . ' (' . $amt . ')' : $name;
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
