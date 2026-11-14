<?php
declare(strict_types=1);

namespace ksfraser\RelatedTo\Tests\Unit\DTO;

use ksfraser\RelatedTo\DTO\AttachmentResolvedDto;
use ksfraser\RelatedTo\DTO\CalendarResolvedDto;
use ksfraser\RelatedTo\DTO\ContactResolvedDto;
use ksfraser\RelatedTo\DTO\CustomerResolvedDto;
use ksfraser\RelatedTo\DTO\InvoiceResolvedDto;
use ksfraser\RelatedTo\DTO\ItemResolvedDto;
use ksfraser\RelatedTo\DTO\MeetingResolvedDto;
use ksfraser\RelatedTo\DTO\OpportunityResolvedDto;
use ksfraser\RelatedTo\DTO\PurchaseOrderResolvedDto;
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\DTO\ResolvedRelatedRecordDto;
use ksfraser\RelatedTo\DTO\SalesOrderResolvedDto;
use ksfraser\RelatedTo\DTO\SupplierResolvedDto;
use ksfraser\RelatedTo\DTO\TaskResolvedDto;
use ksfraser\RelatedTo\DTO\ProjectResolvedDto;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the resolved related-record DTO hierarchy.
 *
 * Each owning module subclasses ResolvedRelatedRecordDto with its own field
 * set and toString()/toHtml() implementation. Consumers may override labels
 * and fields before rendering.
 *
 * @package ksfraser\RelatedTo\Tests\Unit\DTO
 * @since   1.1.0
 */
class ResolvedDtoTest extends TestCase
{
    private function dto(): RelatedRecordDto
    {
        return new RelatedRecordDto('note', '42', 'contact', '7', 'with');
    }

    // --- base class -----------------------------------------------------

    public function testBaseIsAbstract(): void
    {
        $reflection = new \ReflectionClass(ResolvedRelatedRecordDto::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testBaseWrapsDtoAndHoldsFieldsLabelsUrl(): void
    {
        $dto = $this->dto();
        $concrete = new class($dto) extends ResolvedRelatedRecordDto {
            public function toString(): string { return ''; }
            public function toHtml(): string { return ''; }
        };

        $this->assertSame($dto, $concrete->getDto());
        $this->assertSame(array(), $concrete->getFields());
        $this->assertSame(array(), $concrete->getLabels());
        $this->assertSame('#', $concrete->getUrl());
    }

    public function testBaseSettersAreFluent(): void
    {
        $concrete = new class($this->dto()) extends ResolvedRelatedRecordDto {
            public function toString(): string { return ''; }
            public function toHtml(): string { return ''; }
        };

        $this->assertSame($concrete, $concrete->setField('name', 'Alice'));
        $this->assertSame($concrete, $concrete->setLabel('name', 'Full Name'));
        $this->assertSame($concrete, $concrete->setUrl('/view/7'));

        $this->assertSame('Alice', $concrete->getField('name'));
        $this->assertSame('Full Name', $concrete->getLabel('name'));
        $this->assertSame('/view/7', $concrete->getUrl());
        $this->assertSame('Nonexistent', $concrete->getLabel('nonexistent'));
    }

    public function testBaseToArrayIncludesDtoAndFieldsAndLabelsAndUrl(): void
    {
        $concrete = new class($this->dto()) extends ResolvedRelatedRecordDto {
            public function toString(): string { return ''; }
            public function toHtml(): string { return ''; }
        };
        $concrete->setField('name', 'Alice');
        $concrete->setLabel('name', 'Full Name');
        $concrete->setUrl('/view/7');

        $array = $concrete->toArray();
        $this->assertArrayHasKey('lhs_type', $array);
        $this->assertArrayHasKey('fields', $array);
        $this->assertArrayHasKey('labels', $array);
        $this->assertArrayHasKey('url', $array);
        $this->assertSame('Alice', $array['fields']['name']);
        $this->assertSame('Full Name', $array['labels']['name']);
        $this->assertSame('/view/7', $array['url']);
    }

    public function testBaseToTdWrapsToHtml(): void
    {
        $concrete = new class($this->dto()) extends ResolvedRelatedRecordDto {
            public function toString(): string { return 'hello'; }
            public function toHtml(): string { return '<b>hello</b>'; }
        };

        $this->assertSame('<td><b>hello</b></td>', $concrete->toTd());
        $this->assertSame('<td><b>hello</b></td>', $concrete->render());
    }

    public function testBaseToHtmlALinksToUrl(): void
    {
        $concrete = new class($this->dto()) extends ResolvedRelatedRecordDto {
            public function toString(): string { return 'hello'; }
            public function toHtml(): string { return '<b>hello</b>'; }
        };
        $concrete->setUrl('/view/7');

        $this->assertStringContainsString('<a href="/view/7"', $concrete->toHtmlA());
        $this->assertStringContainsString('<b>hello</b>', $concrete->toHtmlA());
        $this->assertStringContainsString('target="_blank"', $concrete->toHtmlA());
    }

    // --- ContactResolvedDto ---------------------------------------------

    public function contactDtoProvider(): array
    {
        return array(
            'first and last' => array('Alice', 'Smith', null, 'Alice Smith'),
            'first only'     => array('Alice', null, null, 'Alice'),
            'last only'      => array(null, 'Smith', null, 'Smith'),
            'with index'     => array('Alice', 'Smith', 7, 'Alice Smith (7)'),
            'empty'          => array(null, null, null, '7'),
        );
    }

    /**
     * @dataProvider contactDtoProvider
     */
    public function testContactResolvedDtoToString($first, $last, $index, $expected): void
    {
        $dto = new ContactResolvedDto($this->dto(), $first, $last, $index);
        $this->assertSame($expected, $dto->toString());
    }

    public function testContactResolvedDtoFields(): void
    {
        $dto = new ContactResolvedDto($this->dto(), 'Alice', 'Smith', 7);

        $this->assertSame('Alice Smith (7)', $dto->getField('displayName'));
        $this->assertSame('Alice', $dto->getField('firstName'));
        $this->assertSame('Smith', $dto->getField('lastName'));
        $this->assertSame(7, $dto->getField('contactIndex'));
    }

    public function testContactResolvedDtoLabels(): void
    {
        $dto = new ContactResolvedDto($this->dto(), 'Alice', 'Smith', null);

        $this->assertSame('Contact', $dto->getLabel('displayName'));
        $this->assertSame('First Name', $dto->getLabel('firstName'));
        $this->assertSame('Last Name', $dto->getLabel('lastName'));
        $this->assertSame('ContactIndex', $dto->getLabel('contactIndex'));

        $dtoWithIndex = new ContactResolvedDto($this->dto(), 'Alice', 'Smith', 7);
        $this->assertSame('Index', $dtoWithIndex->getLabel('contactIndex'));
    }

    public function testContactResolvedDtoOverrideLabels(): void
    {
        $dto = new ContactResolvedDto($this->dto(), 'Alice', 'Smith', null, array(
            'displayName' => 'Full Name',
            'firstName'   => 'Given',
        ));
        $this->assertSame('Full Name', $dto->getLabel('displayName'));
        $this->assertSame('Given', $dto->getLabel('firstName'));
        $this->assertSame('Last Name', $dto->getLabel('lastName'));
    }

    public function testContactResolvedDtoHtmlEscapes(): void
    {
        $dto = new ContactResolvedDto($this->dto(), '<script>', 'Smith', null);
        $this->assertStringContainsString('&lt;script&gt;', $dto->toHtml());
        $this->assertStringNotContainsString('<script>', $dto->toHtml());
    }

    // --- CustomerResolvedDto --------------------------------------------

    public function testCustomerResolvedDtoToString(): void
    {
        $dto = new CustomerResolvedDto($this->dto(), 'Acme Pty Ltd', 'C001');
        $this->assertSame('Acme Pty Ltd (C001)', $dto->toString());

        $dto2 = new CustomerResolvedDto($this->dto(), 'Acme Pty Ltd', '');
        $this->assertSame('Acme Pty Ltd', $dto2->toString());
    }

    public function testCustomerResolvedDtoFields(): void
    {
        $dto = new CustomerResolvedDto($this->dto(), 'Acme Pty Ltd', 'C001');
        $this->assertSame('Acme Pty Ltd', $dto->getField('name'));
        $this->assertSame('C001', $dto->getField('debtorNo'));
        $this->assertSame('Customer', $dto->getLabel('name'));
        $this->assertSame('Debtor No', $dto->getLabel('debtorNo'));
    }

    // --- InvoiceResolvedDto ---------------------------------------------

    public function testInvoiceResolvedDtoToString(): void
    {
        $dto = new InvoiceResolvedDto($this->dto(), 'INV-100', 'PO-5000');
        $this->assertSame('INV-100 / PO-5000', $dto->toString());

        $dto2 = new InvoiceResolvedDto($this->dto(), 'INV-100', '');
        $this->assertSame('INV-100', $dto2->toString());

        $dto3 = new InvoiceResolvedDto($this->dto(), '', 'PO-5000');
        $this->assertSame('PO-5000', $dto3->toString());
    }

    public function testInvoiceResolvedDtoFields(): void
    {
        $dto = new InvoiceResolvedDto($this->dto(), 'INV-100', 'PO-5000');
        $this->assertSame('INV-100', $dto->getField('invoiceNo'));
        $this->assertSame('PO-5000', $dto->getField('reference'));
        $this->assertSame('Invoice No', $dto->getLabel('invoiceNo'));
        $this->assertSame('Reference', $dto->getLabel('reference'));
    }

    // --- ItemResolvedDto ------------------------------------------------

    public function testItemResolvedDtoToString(): void
    {
        $dto = new ItemResolvedDto($this->dto(), 'ITEM-001', 'Widget');
        $this->assertSame('ITEM-001 — Widget', $dto->toString());

        $dto2 = new ItemResolvedDto($this->dto(), 'ITEM-001', '');
        $this->assertSame('ITEM-001', $dto2->toString());
    }

    public function testItemResolvedDtoFields(): void
    {
        $dto = new ItemResolvedDto($this->dto(), 'ITEM-001', 'Widget');
        $this->assertSame('ITEM-001', $dto->getField('stockId'));
        $this->assertSame('Widget', $dto->getField('description'));
        $this->assertSame('Item', $dto->getLabel('stockId'));
        $this->assertSame('Description', $dto->getLabel('description'));
    }

    // --- SupplierResolvedDto --------------------------------------------

    public function testSupplierResolvedDtoToString(): void
    {
        $dto = new SupplierResolvedDto($this->dto(), 'Acme Supplies', 'SUP-001');
        $this->assertSame('Acme Supplies (SUP-001)', $dto->toString());
    }

    public function testSupplierResolvedDtoFields(): void
    {
        $dto = new SupplierResolvedDto($this->dto(), 'Acme Supplies', 'SUP-001');
        $this->assertSame('Acme Supplies', $dto->getField('name'));
        $this->assertSame('SUP-001', $dto->getField('supplierId'));
        $this->assertSame('Supplier', $dto->getLabel('name'));
        $this->assertSame('Supplier Id', $dto->getLabel('supplierId'));
    }

    // --- SalesOrderResolvedDto ------------------------------------------

    public function testSalesOrderResolvedDtoToString(): void
    {
        $dto = new SalesOrderResolvedDto($this->dto(), 'SO-100', 'REF-99');
        $this->assertSame('SO-100 / REF-99', $dto->toString());
    }

    public function testSalesOrderResolvedDtoFields(): void
    {
        $dto = new SalesOrderResolvedDto($this->dto(), 'SO-100', 'REF-99');
        $this->assertSame('SO-100', $dto->getField('orderNo'));
        $this->assertSame('REF-99', $dto->getField('reference'));
        $this->assertSame('Sales Order', $dto->getLabel('orderNo'));
        $this->assertSame('Reference', $dto->getLabel('reference'));
    }

    // --- PurchaseOrderResolvedDto ---------------------------------------

    public function testPurchaseOrderResolvedDtoToString(): void
    {
        $dto = new PurchaseOrderResolvedDto($this->dto(), 'PO-200', 'REF-88');
        $this->assertSame('PO-200 / REF-88', $dto->toString());
    }

    public function testPurchaseOrderResolvedDtoFields(): void
    {
        $dto = new PurchaseOrderResolvedDto($this->dto(), 'PO-200', 'REF-88');
        $this->assertSame('PO-200', $dto->getField('orderNo'));
        $this->assertSame('REF-88', $dto->getField('reference'));
        $this->assertSame('Purchase Order', $dto->getLabel('orderNo'));
        $this->assertSame('Reference', $dto->getLabel('reference'));
    }

    // --- MeetingResolvedDto ---------------------------------------------

    public function testMeetingResolvedDtoToString(): void
    {
        $dto = new MeetingResolvedDto($this->dto(), 'Weekly Sync');
        $this->assertSame('Weekly Sync', $dto->toString());
    }

    public function testMeetingResolvedDtoFields(): void
    {
        $dto = new MeetingResolvedDto($this->dto(), 'Weekly Sync');
        $this->assertSame('Weekly Sync', $dto->getField('name'));
        $this->assertSame('Meeting', $dto->getLabel('name'));
    }

    // --- OpportunityResolvedDto -----------------------------------------

    public function testOpportunityResolvedDtoToString(): void
    {
        $dto = new OpportunityResolvedDto($this->dto(), 'Big Deal', '$5000');
        $this->assertSame('Big Deal ($5000)', $dto->toString());

        $dto2 = new OpportunityResolvedDto($this->dto(), 'Big Deal', '');
        $this->assertSame('Big Deal', $dto2->toString());
    }

    public function testOpportunityResolvedDtoFields(): void
    {
        $dto = new OpportunityResolvedDto($this->dto(), 'Big Deal', '$5000');
        $this->assertSame('Big Deal', $dto->getField('name'));
        $this->assertSame('$5000', $dto->getField('amount'));
        $this->assertSame('Opportunity', $dto->getLabel('name'));
        $this->assertSame('Amount', $dto->getLabel('amount'));
    }

    // --- ProjectResolvedDto ---------------------------------------------

    public function testProjectResolvedDtoToString(): void
    {
        $dto = new ProjectResolvedDto($this->dto(), 'Website Redesign', 'P-10');
        $this->assertSame('Website Redesign (P-10)', $dto->toString());
    }

    public function testProjectResolvedDtoFields(): void
    {
        $dto = new ProjectResolvedDto($this->dto(), 'Website Redesign', 'P-10');
        $this->assertSame('Website Redesign', $dto->getField('name'));
        $this->assertSame('P-10', $dto->getField('projectId'));
        $this->assertSame('Project', $dto->getLabel('name'));
        $this->assertSame('Project Id', $dto->getLabel('projectId'));
    }

    // --- TaskResolvedDto ------------------------------------------------

    public function testTaskResolvedDtoToString(): void
    {
        $dto = new TaskResolvedDto($this->dto(), 'Fix bug');
        $this->assertSame('Fix bug', $dto->toString());
    }

    public function testTaskResolvedDtoFields(): void
    {
        $dto = new TaskResolvedDto($this->dto(), 'Fix bug');
        $this->assertSame('Fix bug', $dto->getField('name'));
        $this->assertSame('Task', $dto->getLabel('name'));
    }

    // --- CalendarResolvedDto --------------------------------------------

    public function testCalendarResolvedDtoToString(): void
    {
        $dto = new CalendarResolvedDto($this->dto(), 'Lunch meeting');
        $this->assertSame('Lunch meeting', $dto->toString());
    }

    public function testCalendarResolvedDtoFields(): void
    {
        $dto = new CalendarResolvedDto($this->dto(), 'Lunch meeting');
        $this->assertSame('Lunch meeting', $dto->getField('title'));
        $this->assertSame('Calendar Entry', $dto->getLabel('title'));
    }

    // --- AttachmentResolvedDto ------------------------------------------

    public function testAttachmentResolvedDtoToString(): void
    {
        $dto = new AttachmentResolvedDto($this->dto(), 'contract.pdf');
        $this->assertSame('contract.pdf', $dto->toString());
    }

    public function testAttachmentResolvedDtoFields(): void
    {
        $dto = new AttachmentResolvedDto($this->dto(), 'contract.pdf');
        $this->assertSame('contract.pdf', $dto->getField('name'));
        $this->assertSame('Attachment', $dto->getLabel('name'));
    }

    // --- all toHtml escape ----------------------------------------------

    public function testAllToHtmlEscape(): void
    {
        $classes = array(
            ContactResolvedDto::class    => array($this->dto(), '<x>', null, null),
            CustomerResolvedDto::class   => array($this->dto(), '<x>', 'C1'),
            InvoiceResolvedDto::class    => array($this->dto(), '<x>', '<y>'),
            ItemResolvedDto::class       => array($this->dto(), '<x>', '<y>'),
            SupplierResolvedDto::class   => array($this->dto(), '<x>', 'S1'),
            SalesOrderResolvedDto::class => array($this->dto(), '<x>', '<y>'),
            PurchaseOrderResolvedDto::class => array($this->dto(), '<x>', '<y>'),
            MeetingResolvedDto::class    => array($this->dto(), '<x>'),
            OpportunityResolvedDto::class => array($this->dto(), '<x>', '<y>'),
            ProjectResolvedDto::class    => array($this->dto(), '<x>', 'P1'),
            TaskResolvedDto::class       => array($this->dto(), '<x>'),
            CalendarResolvedDto::class   => array($this->dto(), '<x>'),
            AttachmentResolvedDto::class => array($this->dto(), '<x>'),
        );

        foreach ($classes as $class => $ctorArgs) {
            $dto = new $class(...$ctorArgs);
            $html = $dto->toHtml();
            $this->assertStringContainsString('&lt;x&gt;', $html, get_class($dto) . ' toHtml() must escape');
            $this->assertStringNotContainsString('<x>', $html, get_class($dto) . ' toHtml() must not contain raw <x>');
        }
    }
}
