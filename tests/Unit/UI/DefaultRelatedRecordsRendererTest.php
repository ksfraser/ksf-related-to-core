<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\UI;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\UI\DefaultRelatedRecordsRenderer;
use PHPUnit\Framework\TestCase;

class DefaultRelatedRecordsRendererTest extends TestCase
{
    private function dto($type, $id, $role = null)
    {
        return new RelatedRecordDto('note', 'N1', $type, $id, $role);
    }

    public function testRendersNoRelatedRecordsMessageWhenEmpty(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer();
        $html = $renderer->renderRelatedRecords('customer', 'C001', array());

        $this->assertStringContainsString('No related records.', $html);
    }

    public function testGroupsRecordsByTypeAndRendersOneTablePerType(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer();
        $html = $renderer->renderRelatedRecords('note', 'N1', array(
            $this->dto('customer', 'C001'),
            $this->dto('opportunity', 'OPP-9'),
            $this->dto('customer', 'C002'),
        ));

        $this->assertStringContainsString('<h2 class="related-title"', $html);
        $this->assertStringContainsString('Customer', $html);
        $this->assertStringContainsString('Opportunity', $html);
        $this->assertStringContainsString('(2)', $html);
        $this->assertStringContainsString('(1)', $html);
        $this->assertStringContainsString('C001', $html);
        $this->assertStringContainsString('OPP-9', $html);
        $this->assertSame(2, substr_count($html, '<table class="related-table'));
    }

    public function testUsesInjectedTypeLabels(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer(array(
            'type_labels' => array('opportunity' => 'Deals'),
        ));
        $html = $renderer->renderRelatedRecords('note', 'N1', array(
            $this->dto('opportunity', 'OPP-9'),
        ));

        $this->assertStringContainsString('Deals', $html);
        $this->assertStringNotContainsString('Opportunity', $html);
    }

    public function testTableBorderAddsBorderedClass(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer(array('table_border' => true));
        $html = $renderer->renderRelatedRecords('note', 'N1', array(
            $this->dto('customer', 'C001'),
        ));

        $this->assertStringContainsString('related-table related-table-bordered', $html);
    }

    public function testUsesInjectedLabelAndUrlResolvers(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer(array(
            'label_resolver' => function ($type, $id) { return 'Label:' . $id; },
            'url_resolver' => function ($type, $id) { return '/view/' . $type . '/' . $id; },
        ));
        $html = $renderer->renderRelatedRecords('note', 'N1', array(
            $this->dto('customer', 'C001'),
        ));

        $this->assertStringContainsString('Label:C001', $html);
        $this->assertStringContainsString('href="/view/customer/C001"', $html);
    }

    public function testEscapesHtmlInLabelsAndRoles(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer();
        $html = $renderer->renderRelatedRecords('note', 'N1', array(
            new RelatedRecordDto('note', 'N1', 'customer', '<script>alert(1)</script>', 'evil"role'),
        ));

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&quot;role', $html);
    }

    public function testUsesInjectedEscapeAndTranslate(): void
    {
        $renderer = new DefaultRelatedRecordsRenderer(array(
            'escape' => function ($value) { return '[' . (string) $value . ']'; },
            'translate' => function ($value) { return strtoupper((string) $value); },
        ));
        $html = $renderer->renderRelatedRecords('note', 'N1', array(
            $this->dto('customer', 'C001'),
        ));

        $this->assertStringContainsString('[REFERENCE]', $html);
        $this->assertStringContainsString('[RECORD]', $html);
    }
}
