<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved task record. Default display is the task name.
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class TaskResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto   The underlying persistence record
     * @param string|null      $name  Task name
     * @param array            $labels Optional per-field label overrides
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $name = null, array $labels = array())
    {
        $fields = array('name' => (string) $name);
        $defaults = array('name' => 'Task');
        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    /**
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toString()
    {
        return (string) $this->getField('name');
    }

    /**
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toHtml()
    {
        return htmlspecialchars((string) $this->getField('name'), ENT_QUOTES, 'UTF-8');
    }
}
