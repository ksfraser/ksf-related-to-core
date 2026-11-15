<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\UI;

/**
 * Relate-records bar — the "add a relationship" control.
 *
 * Renders a Type dropdown cascading to a Record dropdown plus a Relate
 * button, so a context record can be associated with another record through
 * the RelatedTo module (RELATE_RECORDS / RELATED_TO hooks).
 *
 * Framework-agnostic: label/URL/option resolution is injected via callables,
 * so the same class works inside FA (db_query, EntityRegistry, _(), hook
 * responders) or standalone. The consumer handles the POST and dispatches
 * RELATE_RECORDS; this class only renders the form.
 *
 * @BABOK Related: BR-RT-001
 * @since 1.1.0
 */
class RelateRecordsBar
{
    /** @var string Context record type key (lhs) */
    private $lhsType;

    /** @var string|int Context record primary key (lhs) */
    private $lhsId;

    /** @var string Form action URL */
    private $formAction;

    /** @var array<string, string> Type key => label */
    private $typeOptions = array();

    /** @var string Selected type key (may be '') */
    private $selectedType = '';

    /** @var array<string, string> Record id => label for the selected type */
    private $recordOptions = array();

    /** @var callable():array<string,string> Returns all relate-to types */
    private $typeOptionsProvider;

    /** @var callable(string):array<string,string> Returns record options for a type */
    private $recordOptionsProvider;

    /** @var callable(string,string):string Resolves a record label */
    private $labelResolver;

    /** @var callable(string,string):string Resolves a record URL */
    private $urlResolver;

    /** @var callable(string):string HTML escape */
    private $escape;

    /** @var callable(string):string Translate */
    private $translate;

    /**
     * @param array $options Recognised keys:
     *   lhs_type, lhs_id, form_action, submit_label, cancel_label,
     *   type_options (array), type_options_provider (callable),
     *   record_options_provider (callable(string):array), label_resolver,
     *   url_resolver, escape, translate
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function __construct(array $options = array())
    {
        $this->lhsType = isset($options['lhs_type']) ? (string) $options['lhs_type'] : '';
        $this->lhsId   = isset($options['lhs_id'])   ? (string) $options['lhs_id']   : '';
        $this->formAction = isset($options['form_action']) ? (string) $options['form_action'] : '';
        $this->typeOptions = isset($options['type_options']) && is_array($options['type_options'])
            ? $options['type_options'] : array();

        $this->typeOptionsProvider = isset($options['type_options_provider']) && is_callable($options['type_options_provider'])
            ? $options['type_options_provider']
            : array($this, 'defaultTypeOptionsProvider');

        $this->recordOptionsProvider = isset($options['record_options_provider']) && is_callable($options['record_options_provider'])
            ? $options['record_options_provider']
            : array($this, 'defaultRecordOptionsProvider');

        $this->labelResolver = isset($options['label_resolver']) && is_callable($options['label_resolver'])
            ? $options['label_resolver']
            : array($this, 'defaultLabelResolver');

        $this->urlResolver = isset($options['url_resolver']) && is_callable($options['url_resolver'])
            ? $options['url_resolver']
            : array($this, 'defaultUrlResolver');

        $this->escape = isset($options['escape']) && is_callable($options['escape'])
            ? $options['escape']
            : array($this, 'defaultEscape');

        $this->translate = isset($options['translate']) && is_callable($options['translate'])
            ? $options['translate']
            : array($this, 'defaultTranslate');

        // Populate type options from provider when not passed directly.
        if (empty($this->typeOptions) && $this->typeOptionsProvider !== null) {
            $this->typeOptions = (array) call_user_func($this->typeOptionsProvider);
        }

        // Resolve the selected type from options if not explicitly set.
        if (!isset($options['selected_type']) && !empty($this->typeOptions)) {
            $this->selectedType = key($this->typeOptions);
        } elseif (isset($options['selected_type'])) {
            $this->selectedType = (string) $options['selected_type'];
        }

        if ($this->selectedType !== '' && $this->recordOptionsProvider !== null) {
            $this->recordOptions = (array) call_user_func($this->recordOptionsProvider, $this->selectedType);
        }
    }

    /**
     * Render the full relate-to bar HTML.
     *
     * @return string
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function render(): string
    {
        $html = '<fieldset style="margin-top:16px;padding:10px;border:1px solid #ccc;">';
        $html .= '<legend>' .         call_user_func($this->translate,'Relate this record to another record') . '</legend>';
        $html .= '<form method="post" action="' .         call_user_func($this->escape,$this->formAction) . '" style="display:inline-block;margin:0;">';
        $html .= '<input type="hidden" name="relate_record_action" value="1">';
        $html .= '<input type="hidden" name="relate_lhs_type" value="' .         call_user_func($this->escape,$this->lhsType) . '">';
        $html .= '<input type="hidden" name="relate_lhs_id" value="' .         call_user_func($this->escape,(string) $this->lhsId) . '">';
        $html .= '<label for="relate_type">' .         call_user_func($this->translate,'Type') . ':</label> ';
        $html .= $this->renderTypeSelect();
        echo ' ';
        $html .= $this->renderRecordSelect();
        $html .= ' <button type="submit" class="button">' .         call_user_func($this->translate,'Relate') . '</button>';
        $html .= '</form>';
        $html .= '</fieldset>';

        return $html;
    }

    /**
     * Render the type dropdown only.
     *
     * @return string
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function renderTypeSelect(): string
    {
        $html = '<select name="relate_type" id="relate_type">';
        $html .= '<option value="">' .         call_user_func($this->translate,'-- select type --') . '</option>';
        foreach ($this->typeOptions as $key => $label) {
            $selected = $key === $this->selectedType ? ' selected="selected"' : '';
            $html .= '<option value="' .         call_user_func($this->escape,(string) $key) . '"' . $selected . '>'
                .         call_user_func($this->escape,(string) $label) . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Render the record dropdown only (options for the selected type).
     *
     * @return string
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function renderRecordSelect(): string
    {
        $html = '<select name="relate_record" id="relate_record"';
        if (empty($this->recordOptions)) {
            $html .= ' disabled="disabled"';
        }
        $html .= '>';
        $html .= '<option value="">' .         call_user_func($this->translate,'-- select record --') . '</option>';
        foreach ($this->recordOptions as $id => $label) {
            $html .= '<option value="' .         call_user_func($this->escape,(string) $id) . '">'
                .         call_user_func($this->escape,(string) $label) . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * Switch the selected type and rebuild record options.
     *
     * @param string $type Type key
     * @return $this
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function setSelectedType(string $type): self
    {
        $this->selectedType = $type;
        if ($type !== '' && $this->recordOptionsProvider !== null) {
            $this->recordOptions = (array) call_user_func($this->recordOptionsProvider, $type);
        } else {
            $this->recordOptions = array();
        }

        return $this;
    }

    /**
     * @return array<string,string>
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function getTypeOptions(): array
    {
        return $this->typeOptions;
    }

    /**
     * @return array<string,string>
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function getRecordOptions(): array
    {
        return $this->recordOptions;
    }

    /**
     * @return string
     * @BABOK Related: BR-RT-001
     * @since 1.1.0
     */
    public function getSelectedType(): string
    {
        return $this->selectedType;
    }

    // ------------------------------------------------------------------
    // Default callables
    // ------------------------------------------------------------------

    private function defaultTypeOptionsProvider(): array
    {
        return array();
    }

    private function defaultRecordOptionsProvider(string $type): array
    {
        return array();
    }

    private function defaultLabelResolver(string $type, string $id): string
    {
        return $id;
    }

    private function defaultUrlResolver(string $type, string $id): string
    {
        return '#';
    }

    private function defaultEscape(string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function defaultTranslate(string $value): string
    {
        return (string) $value;
    }
}
