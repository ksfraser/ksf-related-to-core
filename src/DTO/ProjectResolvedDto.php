<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved project record. Default display is the project name with the
 * project id as a secondary field.
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class ProjectResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto      The underlying persistence record
     * @param string|null      $name     Project name
     * @param string|null      $projectId Project id
     * @param array            $labels   Optional per-field label overrides
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, $name = null, $projectId = null, array $labels = array())
    {
        $fields = array(
            'name'      => (string) $name,
            'projectId' => (string) $projectId,
        );
        $defaults = array(
            'name'      => 'Project',
            'projectId' => 'Project Id',
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
        $id   = (string) $this->getField('projectId');
        return $id !== '' ? $name . ' (' . $id . ')' : $name;
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
