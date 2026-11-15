<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\UI;

use ksfraser\RelatedTo\UI\RelateRecordsBar;
use PHPUnit\Framework\TestCase;

class RelateRecordsBarTest extends TestCase
{
    public function testRenderContainsFieldsetFormAndControls(): void
    {
        $bar = new RelateRecordsBar(array(
            'lhs_type' => 'note',
            'lhs_id' => '5',
            'form_action' => 'notes.php?view=form',
            'type_options' => array('customer' => 'Customer', 'contact' => 'Contact'),
        ));
        $html = $bar->render();

        $this->assertStringContainsString('<fieldset', $html);
        $this->assertStringContainsString('Relate this record to another record', $html);
        $this->assertStringContainsString('name="relate_record_action"', $html);
        $this->assertStringContainsString('name="relate_lhs_type" value="note"', $html);
        $this->assertStringContainsString('name="relate_lhs_id" value="5"', $html);
        $this->assertStringContainsString('name="relate_type"', $html);
        $this->assertStringContainsString('name="relate_record"', $html);
        $this->assertStringContainsString('Relate', $html);
        $this->assertStringContainsString('action="notes.php?view=form"', $html);
    }

    public function testRenderSelectsTheDefaultTypeAndBuildsRecordOptions(): void
    {
        $bar = new RelateRecordsBar(array(
            'lhs_type' => 'note',
            'lhs_id' => '5',
            'form_action' => 'notes.php',
            'type_options_provider' => function () { return array('customer' => 'Customer', 'contact' => 'Contact'); },
            'record_options_provider' => function ($type) { return array('C001' => 'Acme'); },
        ));
        $html = $bar->render();

        $this->assertStringContainsString('<option value="customer" selected="selected">Customer</option>', $html);
        $this->assertStringContainsString('<option value="C001">Acme</option>', $html);
    }

    public function testSetSelectedTypeRebuildsRecordOptions(): void
    {
        $bar = new RelateRecordsBar(array(
            'lhs_type' => 'note',
            'lhs_id' => '5',
            'form_action' => 'notes.php',
            'type_options' => array('customer' => 'Customer', 'contact' => 'Contact'),
            'record_options_provider' => function ($type) { return array('C001' => 'Acme'); },
        ));
        $bar->setSelectedType('contact');

        $this->assertSame('contact', $bar->getSelectedType());
        $this->assertSame(array('C001' => 'Acme'), $bar->getRecordOptions());
        $this->assertSame(array('customer' => 'Customer', 'contact' => 'Contact'), $bar->getTypeOptions());
    }

    public function testRenderTypeSelectDefaultsToFirstType(): void
    {
        $bar = new RelateRecordsBar(array(
            'lhs_type' => 'note',
            'lhs_id' => '5',
            'form_action' => 'notes.php',
            'type_options' => array('customer' => 'Customer'),
        ));
        $html = $bar->renderTypeSelect();

        $this->assertStringContainsString('<option value="">-- select type --</option>', $html);
        $this->assertStringContainsString('<option value="customer" selected="selected">Customer</option>', $html);
    }

    public function testRenderRecordSelectIsDisabledWhenEmpty(): void
    {
        $bar = new RelateRecordsBar(array(
            'lhs_type' => 'note',
            'lhs_id' => '5',
            'form_action' => 'notes.php',
            'type_options' => array('customer' => 'Customer'),
            'record_options_provider' => function ($type) { return array(); },
        ));
        $html = $bar->render();

        $this->assertStringContainsString('disabled="disabled"', $html);
    }

    public function testEscapesFormActionAndLabels(): void
    {
        $bar = new RelateRecordsBar(array(
            'lhs_type' => 'note',
            'lhs_id' => '5',
            'form_action' => 'notes.php?x=<script>',
            'type_options' => array('customer' => 'Cust<span>omer'),
        ));
        $html = $bar->render();

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringContainsString('Cust&lt;span&gt;omer', $html);
    }
}
