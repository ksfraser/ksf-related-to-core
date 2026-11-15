# Use Cases — ksfraser/related-to-core

**Package:** `ksfraser/related-to-core`
**Version:** `1.0.0`

---

## UC-RT-001 — Associate two records

**Actor:** Calling module (Notes, CRM, HRM, etc.)
**Goal:** Record that record A is related to record B with a specific role.

**Preconditions:**
- Both records exist in their respective tables.
- Both entity types are registered.

**Main success scenario:**
1. Caller builds a `RelatedRecordDto` with lhs_type, lhs_id, rhs_type, rhs_id, link_role.
2. Caller invokes `RELATE_RECORDS` hook with the DTO by reference.
3. FA adapter's `RelatedRecordRepository::relate()` persists the association.
4. Hook returns `['ok' => true]`.

**Extensions:**
- If RelatedTo module is not active, hook returns nothing and association is not recorded.

---

## UC-RT-002 — Fetch records related to a context record

**Actor:** Calling module rendering a record detail page
**Goal:** Display all records related to the current record, grouped by type.

**Preconditions:**
- Context record exists.

**Main success scenario:**
1. Caller builds a `RelatedQueryDto` with lhs_type, lhs_id.
2. Caller invokes `RELATED_TO` hook with the query by reference.
3. FA adapter's `RelatedRecordRepository::relatedTo()` queries the master table.
4. Hook returns `['ok' => true, 'related' => [RelatedRecordDto, ...]]`.
5. Caller renders the related records using a `RelatedRecordsRenderer` implementation,
   grouped by rhs_type under collapsible section headers.

---

## UC-RT-003 — Register a module's related types

**Actor:** Any module (CRM, HRM, Calendar, etc.)
**Goal:** Advertise that this module's record types can be related to other records, and supply the table/pk/label definition so resolvers can render labels.

**Preconditions:**
- The module extends `ksfraser\FrontAccounting\Common\Repository\EntityRegistry` (or contributes via hook).

**Main success scenario:**
1. Module implements a `RELATED_MODULES` responder in its hooks class.
2. Responder appends `['type' => 'meeting', 'label' => 'Meetings', 'module' => 'ksf_FA_CRM']` to $data['modules'].
3. Module also answers `RELATED_ENTITY_TYPES` with `['types' => ['meeting' => ['table' => 'crm_meetings', 'pk' => 'id', 'label' => 'title', ...]]]`.
4. Notes (or any consumer) reads the broadcast list to populate its associate Type DDL, and uses the type definition to resolve labels and URLs.

**Extensions:**
- If RelatedTo module is not active, hook returns nothing and association is not recorded.
- If no `RELATED_ENTITY_TYPES` answer exists for a type, label resolution falls back to the raw id.