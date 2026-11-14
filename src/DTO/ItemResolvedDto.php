<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved item record. Default display is the item code with an optional
 * description.
 *
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class ItemResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto        The underlying persistence record
     * @param string|null      $stockId    Item code / stock id
     * @param string|null      $description Item description
     * @param array            $labels     Optional per-field label overrides
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $stockId = null, $description = null, array $labels = array())
    {
        $fields = array(
            'stockId'    => (string) $stockId,
            'description' => (string) $description,
        );

        $defaults = array(
            'stockId'    => 'Item',
            'description'=> 'Description',
        );

        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    public function toString()
    {
        $code = (string) $this->getField('stockId');
        $desc = (string) $this->getField('description');
        return $desc !== '' ? $code . ' — ' . $desc : $code;
    }

    public function toHtml()
    {
        return htmlspecialchars($this->toString(), ENT_QUOTES, 'UTF-8');
    }
}
