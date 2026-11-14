# Business Requirements — ksfraser/related-to-core

**Package:** `ksfraser/related-to-core`
**Version:** `1.0.0`
**Namespace:** `ksfraser\RelatedTo\*`
**Module sequence:** RT (RelatedTo)

---

## BR-RT-001 — Generic related-record association

**Status:** IMPLEMENTED

The system SHALL provide a single, platform-independent contract for recording and querying
"record A is related to record B" associations, so that multiple modules (Notes, CRM, HRM,
Calendar, Project Management) can relate their records without knowing each other's table
schemas.

1. An association SHALL consist of: lhs_type, lhs_id, rhs_type, rhs_id, link_role.
2. `lhs_type`/`lhs_id` SHALL identify the context record being viewed.
3. `rhs_type`/`rhs_id` SHALL identify the related record.
4. `link_role` SHALL describe WHY the records are related (`regarding`, `with`,
   `attendee`, `document`, `reference`).
5. The association SHALL be polymorphic: any entity type may relate to any other entity type.

---

## BR-RT-002 — Repository contract

**Status:** IMPLEMENTED

The system SHALL define a `RelatedRecordRepositoryInterface` with methods: relate,
unrelate, relatedTo, relatedBy, isRelated.

1. `relate()` SHALL create an association (or no-op when it already exists).
2. `unrelate()` SHALL remove an association.
3. `relatedTo()` SHALL return all records related to a context record, optionally filtered
   by rhs type, with limit/offset paging.
4. `relatedBy()` SHALL return all records that reference a target record (reverse lookup).
5. `isRelated()` SHALL return whether an association exists.

---

## BR-RT-003 — DTOs

**Status:** IMPLEMENTED

The system SHALL provide `RelatedRecordDto` and `RelatedQueryDto` value objects.

1. `RelatedRecordDto` SHALL carry lhs_type, lhs_id, rhs_type, rhs_id, link_role.
2. `RelatedQueryDto` SHALL carry lhs_type, lhs_id, rhs_type (optional), limit, offset.
3. Both SHALL support `toArray()` and `fromArray()` serialization.

---

## BR-RT-004 — Exceptions

**Status:** IMPLEMENTED

The system SHALL define a base `RelatedRecordException` with subclasses:
`UnknownRelatedTypeException` and `RelatedRecordPersistenceException`.

---

## BR-RT-005 — UI renderer contract

**Status:** IMPLEMENTED

The system SHALL define `RelatedRecordsRendererInterface` with a single method:
`renderRelatedRecords()` that groups related records by rhs type under collapsible
section headers (SuiteCRM-style).

---

## BR-RT-006 — Entity type registry

**Status:** IMPLEMENTED

The system SHALL provide a shared entity type registry so resolvers can determine
the table name, primary key, and label column for any related record type without
raw SQL against another module's tables.

1. The registry SHALL be hosted in `ksfraser\FrontAccounting\Common\Repository\EntityRegistry`
   as the base definition of FA-native entity types.
2. Each module SHALL extend the base registry and register its own entity types
   (table, pk, label, picker, link roles).
3. Modules SHALL contribute types via the `RELATED_ENTITY_TYPES` hook, fired
   inside `Common\EntityRegistry::buildRegistry()`.
4. Type keys SHALL be namespaced to their owning module (`crm_contact`,
   `calendar_meeting`, `invoice_invoice`, etc.); a module must not register a
   type it does not own.
5. Bare keys (`contact`, `invoice`, `meeting`) in `ksf_FA_Notes\EntityRegistry`
   are a stopgap until the owning modules come online.
6. Link roles are declared per module via `linkRoles()`; the base provides
   `defaultLinkRoles()` (`['reference' => 'Reference']`) as fallback.
7. The hook was renamed from `NOTES_ENTITY_TYPES` to `RELATED_ENTITY_TYPES`
   because the types serve RelatedTo lookups, not Notes exclusively.

---

## BR-RT-007 — Resolved display DTOs

**Status:** IMPLEMENTED

The system SHALL provide `ResolvedRelatedRecordDto` — an abstract base class in
`ksfraser\RelatedTo\DTO\` — that wraps a `RelatedRecordDto` with resolved
display fields, overridable per-field labels, and render methods.

1. `ResolvedRelatedRecordDto` SHALL wrap a `RelatedRecordDto` (the persistence
   contract mapping 1:1 to `0_ksf_RelatedTo`).
2. It SHALL expose `fields[]` (resolved display values), `labels[]`
   (per-field display labels, consumer-overridable), and `url` (link target).
3. It SHALL provide `toString()`, `toHtml()` (abstract, implemented by
   subclasses), `toHtmlA()` (anchor wrapper), `toTd()` (table cell wrapper),
   and `render()` (alias for `toTd()`).
4. Each related record type SHALL have a concrete subclass:
   `ContactResolvedDto`, `CustomerResolvedDto`, `InvoiceResolvedDto`,
   `ItemResolvedDto`, `SupplierResolvedDto`, `SalesOrderResolvedDto`,
   `PurchaseOrderResolvedDto`, `MeetingResolvedDto`, `OpportunityResolvedDto`,
   `ProjectResolvedDto`, `TaskResolvedDto`, `CalendarResolvedDto`,
   `AttachmentResolvedDto`.
5. `lhs`/`rhs` remain interchangeable; the wrapped DTO carries both identities.

---

## BR-RT-008 — RELATED_TO_FIELDS hook

**Status:** IMPLEMENTED

The system SHALL provide a `RELATED_TO_FIELDS` hook so the owning module of a
related record type can declare the display fields it wants shown for that
type.

1. The hook payload carries `['module' => <consumer>, 'type' => <rhs type>,
   'fields' => &[]]` by reference.
2. `fields` maps a DTO field name to its human display label, e.g.
   `['invoiceNo' => 'Invoice No', 'reference' => 'Reference']`.
3. The owning module answers with its own fields; a consuming module may
   override labels before calling `render()`/`toHtml()`.
4. RelatedTo itself owns no record type and contributes no fields — each
   owning module (CRM, Customer, Invoice, …) answers this hook.

