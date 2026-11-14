<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved calendar entry record. Default display is the entry title.
 *
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class CalendarResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto   The underlying persistence record
     * @param string|null      $title Calendar entry title
     * @param array            $labels Optional per-field label overrides
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $title = null, array $labels = array())
    {
        $fields = array('title' => (string) $title);
        $defaults = array('title' => 'Calendar Entry');
        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    public function toString()
    {
        return (string) $this->getField('title');
    }

    public function toHtml()
    {
        return htmlspecialchars((string) $this->getField('title'), ENT_QUOTES, 'UTF-8');
    }
}
