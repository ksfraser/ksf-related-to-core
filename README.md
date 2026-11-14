# ksfraser/related-to-core

Framework-agnostic related-records core for the KSF ecosystem.

## What it does

Provides the shared contracts, DTOs, and exception types for recording and querying
"record A is related to record B" associations — the polymorphic cross-reference layer
that lets Notes, CRM, HRM, Calendar, Project Management, and any other module relate
their records to one another without knowing each other's table schemas.

This is the **platform-independent** layer. It contains no database access, no framework
hooks, and no UI — only the contracts that platform adapters implement.

## Package layout

| Path | Purpose |
|------|---------|
| `src/DTO/RelatedRecordDto.php` | One association: lhs_type/lhs_id/rhs_type/rhs_id/link_role |
| `src/DTO/RelatedQueryDto.php` | Search criteria for "what is related to this record?" |
| `src/DTO/ResolvedRelatedRecordDto.php` | Abstract base: wraps RelatedRecordDto with resolved fields, labels, render methods |
| `src/DTO/ContactResolvedDto.php` | Resolved contact: First + Last name, optional index |
| `src/DTO/CustomerResolvedDto.php` | Resolved customer: name + debtor number |
| `src/DTO/InvoiceResolvedDto.php` | Resolved invoice: invoice number + reference |
| `src/DTO/ItemResolvedDto.php` | Resolved item: stock id + description |
| `src/DTO/SupplierResolvedDto.php` | Resolved supplier: name + supplier id |
| `src/DTO/SalesOrderResolvedDto.php` | Resolved sales order: order number + reference |
| `src/DTO/PurchaseOrderResolvedDto.php` | Resolved purchase order: PO number + reference |
| `src/DTO/MeetingResolvedDto.php` | Resolved meeting: name |
| `src/DTO/OpportunityResolvedDto.php` | Resolved opportunity: name + amount |
| `src/DTO/ProjectResolvedDto.php` | Resolved project: name + project id |
| `src/DTO/TaskResolvedDto.php` | Resolved task: name |
| `src/DTO/CalendarResolvedDto.php` | Resolved calendar entry: title |
| `src/DTO/AttachmentResolvedDto.php` | Resolved attachment: name |
| `src/Repository/RelatedRecordRepositoryInterface.php` | Contract: relate, unrelate, relatedTo, relatedBy, isRelated |
| `src/Exception/RelatedRecordException.php` | Base + persistence/unknown-type exceptions |
| `src/UI/RelatedRecordsRendererInterface.php` | Contract: SuiteCRM-style twisty-section renderer |

## Data model

A single association row: `lhs_type`, `lhs_id`, `rhs_type`, `rhs_id`, `link_role`.

- `lhs_type` / `lhs_id` — the context record being viewed
- `rhs_type` / `rhs_id` — the related record
- `link_role` — WHY they are related (`regarding`, `with`, `attendee`, `document`, `reference`)

`lhs`/`rhs` are interchangeable: the relationship is symmetric, and
`relatedSymmetric()` normalizes so the context record is always on the lhs.

## Hooks (implemented by the FA adapter module)

The FA adapter module `ksfraser/ksf-fa-related-to` answers these hook names:

| Hook | Direction | Payload | Reply |
|------|-----------|---------|-------|
| `RELATE_RECORDS` | request | `RelatedRecordDto` by reference | `['ok'=>bool, 'error'=>string]` |
| `RELATED_TO` | request | `RelatedQueryDto` by reference | `['ok'=>bool, 'related'=>RelatedRecordDto[]]` |
| `RELATED_MODULES` | broadcast | `['modules' => []]` by reference | each responder appends `['type', 'label', 'module']` |
| `RELATED_TO_FIELDS` | broadcast | `['module', 'type', 'fields' => &[]]` | owning module sets `['fieldName' => 'Display Label']` |

## Display resolution

Persistence (`RelatedRecordDto`) and display (`ResolvedRelatedRecordDto`) are
separate concerns. The owning module builds a resolved DTO subclass in its
`RELATED_TO` responder, pre-resolving label, URL, invoice number, reference,
etc. The renderer calls `toHtml()`/`toHtmlA()`/`toTd()` — it never runs raw
SQL. Contact display (First + Last name, optional index) defaults through
`ksf_FA_Common`'s `ContactResolver` (hook-first via `ksf_contact_resolve`);
CRM or a future `ksf_FA_Contact` overrides by registering a listener.

## Installation

```bash
composer require ksfraser/related-to-core
```

Requires PHP >= 7.3. No third-party dependencies.

## Usage example

```php
use ksfraser\RelatedTo\DTO\RelatedRecordDto;
use ksfraser\RelatedTo\DTO\RelatedQueryDto;

// Associate note #42 with opportunity #1001
$dto = new RelatedRecordDto('note', 42, 'opportunity', 1001, 'regarding');
$result = $dto;
hook_invoke_first('ksf_FA_RelatedTo', 'RELATE_RECORDS', $result);
// $result['ok'] === true

// Find everything related to customer #C001
$query = new RelatedQueryDto('customer', 'C001');
$result = $query;
hook_invoke_first('ksf_FA_RelatedTo', 'RELATED_TO', $result);
// $result['related'] is an array of RelatedRecordDto
```

## Testing

```bash
composer install --dev
vendor/bin/phpunit
```

## Repository

https://github.com/ksfraser/ksf-related-to-core
