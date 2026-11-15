<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Base class for a resolved, display-ready related record.
 *
 * Wraps a RelatedRecordDto (the persistence contract, mapping 1:1 to
 * 0_ksf_RelatedTo) with resolved display fields, overridable per-field labels,
 * and render methods. lhs/rhs remain interchangeable; the wrapper carries the
 * context record's identity via the wrapped DTO.
 *
 * Each module that owns a related record type subclasses this and provides its
 * own field set plus toString()/toHtml() implementations. A consumer may
 * override labels (setLabel()/setLabels()) or fields (setField()/setFields())
 * before calling render()/toHtml()/toHtmlA()/toTd().
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
abstract class ResolvedRelatedRecordDto
{
    /** @var RelatedRecordDto The underlying persistence record */
    public $dto;

    /** @var array<string, mixed> Resolved display fields, e.g. ['name' => 'Alice', 'invoiceNo' => 'INV-100'] */
    public $fields = array();

    /** @var array<string, string> Per-field display labels, keyed by field name */
    public $labels = array();

    /** @var string URL to link to, or '#' when none */
    public $url = '#';

    /**
     * @param RelatedRecordDto $dto     The underlying persistence record
     * @param array            $fields  Resolved display fields
     * @param array            $labels  Per-field display labels
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(RelatedRecordDto $dto, array $fields = array(), array $labels = array())
    {
        $this->dto = $dto;
        $this->fields = $fields;
        $this->labels = $labels;
    }

    /**
     * @return RelatedRecordDto
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function getDto()
    {
        return $this->dto;
    }

    /**
     * @param string $field
     * @param mixed  $value
     * @return $this
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function setField($field, $value)
    {
        $this->fields[$field] = $value;

        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $field
     * @return mixed|null
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function getField($field)
    {
        return isset($this->fields[$field]) ? $this->fields[$field] : null;
    }

    /**
     * @return array<string, mixed>
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $field
     * @param string $label
     * @return $this
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function setLabel($field, $label)
    {
        $this->labels[$field] = (string) $label;

        return $this;
    }

    /**
     * @param array $labels
     * @return $this
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param string $field
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function getLabel($field)
    {
        return isset($this->labels[$field]) ? (string) $this->labels[$field] : ucfirst((string) $field);
    }

    /**
     * @return array<string, string>
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string $url
     * @return $this
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;

        return $this;
    }

    /**
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return array<string, mixed>
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toArray()
    {
        return array_merge($this->dto->toArray(), array(
            'fields' => $this->fields,
            'labels' => $this->labels,
            'url'    => $this->url,
        ));
    }

    /**
     * Plain-text representation.
     *
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    abstract public function toString();

    /**
     * HTML-ready representation (already escaped where appropriate).
     *
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    abstract public function toHtml();

    /**
     * Wrap toHtml() in an anchor tag.
     *
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toHtmlA()
    {
        return '<a href="' . htmlspecialchars((string) $this->url, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">'
            . $this->toHtml() . '</a>';
    }

    /**
     * Wrap toHtml() in a table cell.
     *
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function toTd()
    {
        return '<td>' . $this->toHtml() . '</td>';
    }

    /**
     * Alias for toTd(), convenient for renderer consumption.
     *
     * @return string
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function render()
    {
        return $this->toTd();
    }
}
