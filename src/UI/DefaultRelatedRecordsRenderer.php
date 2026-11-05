<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\UI;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Default framework-agnostic related-records renderer.
 *
 * Renders related records grouped by rhs type under collapsible (twisty)
 * section headers. Label/URL resolution is injected via callables so the
 * same class works inside FA (db_query, EntityRegistry, _()) or standalone.
 *
 * Border rendering is opt-in via the `table_border` option; when enabled the
 * table, th, and td cells all receive 1px borders.
 *
 * @since 1.0.1
 */
class DefaultRelatedRecordsRenderer implements RelatedRecordsRendererInterface
{
    /** @var callable(string, string):string Resolves a record label */
    private $labelResolver;

    /** @var callable(string):string Resolves a type label */
    private $typeLabelResolver;

    /** @var callable(string, string):string Resolves a record URL */
    private $urlResolver;

    /** @var callable(string):string Escapes HTML for output */
    private $escape;

    /** @var callable(string):string Translates a string, or identity if _() missing */
    private $translate;

    private $cssClass;
    private $headerClass;
    private $tableClass;
    private $tableBordered;
    private $typeLabels;
    private $removeResolver;

    /**
     * @param array $options Recognised keys:
     *   css_class, header_class, table_class, table_border (bool),
     *   type_labels (array), label_resolver (callable),
     *   type_label_resolver (callable), url_resolver (callable),
     *   remove_resolver (callable), escape (callable), translate (callable)
     * @since 1.0.1
     */
    public function __construct(array $options = array())
    {
        $this->labelResolver = isset($options['label_resolver']) && is_callable($options['label_resolver'])
            ? $options['label_resolver']
            : array($this, 'defaultLabelResolver');

        $this->typeLabelResolver = isset($options['type_label_resolver']) && is_callable($options['type_label_resolver'])
            ? $options['type_label_resolver']
            : array($this, 'defaultTypeLabelResolver');

        $this->urlResolver = isset($options['url_resolver']) && is_callable($options['url_resolver'])
            ? $options['url_resolver']
            : array($this, 'defaultUrlResolver');

        $this->removeResolver = isset($options['remove_resolver']) && is_callable($options['remove_resolver'])
            ? $options['remove_resolver']
            : array($this, 'defaultRemoveResolver');

        $this->escape = isset($options['escape']) && is_callable($options['escape'])
            ? $options['escape']
            : array($this, 'defaultEscape');

        $this->translate = isset($options['translate']) && is_callable($options['translate'])
            ? $options['translate']
            : array($this, 'defaultTranslate');

        $this->cssClass = isset($options['css_class']) ? (string) $options['css_class'] : 'related-records';
        $this->headerClass = isset($options['header_class']) ? (string) $options['header_class'] : 'related-header';
        $this->tableClass = isset($options['table_class']) ? (string) $options['table_class'] : 'related-table';
        $this->tableBordered = !empty($options['table_border']);
        $this->typeLabels = isset($options['type_labels']) && is_array($options['type_labels'])
            ? $options['type_labels']
            : array();
    }

    /**
     * {@inheritdoc}
     * @since 1.0.1
     */
    public function renderRelatedRecords(
        $lhsType,
        $lhsId,
        array $related,
        array $options = array()
    ) {
        $cssClass = isset($options['css_class']) ? (string) $options['css_class'] : $this->cssClass;
        $headerClass = isset($options['header_class']) ? (string) $options['header_class'] : $this->headerClass;
        $tableClass = isset($options['table_class']) ? (string) $options['table_class'] : $this->tableClass;
        $tableBordered = isset($options['table_border']) ? !empty($options['table_border']) : $this->tableBordered;
        $typeLabels = isset($options['type_labels']) && is_array($options['type_labels'])
            ? $options['type_labels']
            : $this->typeLabels;

        // Group related records by rhs type
        $groups = array();
        foreach ($related as $record) {
            $type = $record->rhsType;
            if (!isset($groups[$type])) {
                $groups[$type] = array();
            }
            $groups[$type][] = $record;
        }

        if (empty($groups)) {
            $msg = $this->esc($this->translate('No related records.'));
            return '<p class="' . $msg . '">' . $msg . '</p>';
        }

        $html = '<div class="' . $this->esc($cssClass) . '">';

        // Sort groups by type label for stable display
        ksort($groups);

        foreach ($groups as $type => $records) {
            if (isset($typeLabels[$type])) {
                $label = (string) $typeLabels[$type];
            } else {
                $label = (string) call_user_func($this->typeLabelResolver, $type);
            }

            $count = count($records);
            $toggleId = 'rel-toggle-' . $this->esc($type) . '-' . $this->esc((string) $lhsId);

            $html .= '<div class="' . $this->esc($headerClass) . '">';
            $html .= '<h2 class="related-title" onclick="return toggleRelated(\'' . $this->escJs($toggleId) . '\');">';
            $html .= '<span class="rel-arrow expanded"></span> ' . $this->esc($label);
            $html .= ' <span class="rel-count">(' . $count . ')</span>';
            $html .= '</h2>';
            $html .= '</div>';

            $html .= '<div id="' . $this->esc($toggleId) . '" class="rel-content" style="display:block;">';
            $tableClasses = $tableClass;
            if ($tableBordered) {
                $tableClasses .= ' related-table-bordered';
            }
            $html .= '<table class="' . $this->esc($tableClasses) . '">';
            $html .= '<thead><tr>';
            $html .= '<th>' . $this->esc($this->translate('Record')) . '</th>';
            $html .= '<th>' . $this->esc($this->translate('Role')) . '</th>';
            $html .= '<th></th>';
            $html .= '</tr></thead>';
            $html .= '<tbody>';

            foreach ($records as $record) {
                $label = (string) call_user_func($this->labelResolver, $record->rhsType, $record->rhsId);
                $url = (string) call_user_func($this->urlResolver, $record->rhsType, $record->rhsId);
                $role = $record->linkRole !== null ? $record->linkRole : $this->translate('reference');

                $html .= '<tr>';
                $html .= '<td>';
                if ($url !== '#') {
                    $html .= '<a href="' . $this->esc($url) . '" target="_blank" rel="noopener noreferrer">' . $this->esc($label) . '</a>';
                } else {
                    $html .= $this->esc($label);
                }
                $html .= '</td>';
                $html .= '<td>' . $this->esc($role) . '</td>';
                $html .= '<td>' . $this->esc(call_user_func($this->removeResolver, $record, (string) $lhsId)) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<style>'
            . '.related-title { font-size: 1.15em; font-weight: bold; margin: 0 0 0.5em 0; cursor: pointer; }'
            . '.related-title .rel-arrow { display: inline-block; width: 0; height: 0; margin-right: 6px; vertical-align: middle; }'
            . '.related-title .rel-arrow.expanded { border-top: 6px solid #555; border-left: 4px solid transparent; border-right: 4px solid transparent; border-bottom: none; }'
            . '.related-title .rel-arrow.collapsed { border-left: 6px solid #555; border-top: 4px solid transparent; border-bottom: 4px solid transparent; border-right: none; }'
            . 'table.related-table-bordered, table.related-table-bordered th, table.related-table-bordered td { border: 1px solid #999; border-collapse: collapse; }'
            . 'table.related-table-bordered { border: 1px solid #999; }'
            . '</style>';
        $html .= '<script>'
            . 'function toggleRelated(id) {'
            . ' var el = document.getElementById(id);'
            . ' if (!el) return false;'
            . ' var arrow = el.previousElementSibling.querySelector(".rel-arrow");'
            . ' if (el.style.display === "none") { el.style.display = "block"; if (arrow) arrow.className = "rel-arrow expanded"; }'
            . ' else { el.style.display = "none"; if (arrow) arrow.className = "rel-arrow collapsed"; }'
            . ' return false;'
            . '}'
            . '</script>';

        return $html;
    }

    private function defaultLabelResolver($type, $id)
    {
        return (string) $id;
    }

    private function defaultTypeLabelResolver($type)
    {
        return ucfirst((string) $type);
    }

    private function defaultUrlResolver($type, $id)
    {
        return '#';
    }

    private function defaultEscape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function defaultTranslate($value)
    {
        if (function_exists('_')) {
            return _( $value );
        }
        return (string) $value;
    }

    private function esc($value)
    {
        return (string) call_user_func($this->escape, $value);
    }

    private function escJs($value)
    {
        return str_replace(array('\\', "'"), array('\\\\', "\\'"), (string) $value);
    }
}
