# Functional Requirements — ksfraser/related-to-core

**Package:** `ksfraser/related-to-core`
**Version:** `1.0.0`

Status legend: **IMPLEMENTED** / **PARTIAL** / **NOT STARTED**

---

## FR-RT-001-001 — RelatedRecordDto construction

**Status:** IMPLEMENTED
**Code:** `src/DTO/RelatedRecordDto.php`
**Tests:** `tests/Unit/DTO/RelatedRecordDtoTest.php`

1. `RelatedRecordDto` SHALL accept lhs_type, lhs_id, rhs_type, rhs_id, link_role in its constructor.
2. `lhsType` SHALL be normalized to string.
3. `rhsType` SHALL be normalized to string.
4. `linkRole` SHALL default to null when omitted.
5. `toArray()` SHALL return keys: lhs_type, lhs_id, rhs_type, rhs_id, link_role.
6. `fromArray()` SHALL reconstruct a DTO from a serialized array.

---

## FR-RT-001-002 — RelatedQueryDto construction

**Status:** IMPLEMENTED
**Code:** `src/DTO/RelatedQueryDto.php`
**Tests:** `tests/Unit/DTO/RelatedQueryDtoTest.php`

1. `RelatedQueryDto` SHALL accept lhs_type, lhs_id, rhs_type (optional), limit (optional), offset (optional).
2. `lhsType` SHALL be normalized to string.
3. `rhsType` SHALL be null when omitted.
4. `toArray()` SHALL return keys: lhs_type, lhs_id, rhs_type, limit, offset.
5. `fromArray()` SHALL reconstruct a DTO from a serialized array.

---

## FR-RT-001-003 — Repository contract methods

**Status:** IMPLEMENTED
**Code:** `src/Repository/RelatedRecordRepositoryInterface.php`
**Tests:** `tests/Unit/Repository/RelatedRecordRepositoryInterfaceTest.php`

1. `relate(RelatedRecordDto)` SHALL return bool.
2. `unrelate(lhsType, lhsId, rhsType, rhsId, linkRole)` SHALL return bool.
3. `relatedTo(RelatedQueryDto)` SHALL return `RelatedRecordDto[]`.
4. `relatedBy(rhsType, rhsId, lhsType)` SHALL return `RelatedRecordDto[]`.
5. `isRelated(lhsType, lhsId, rhsType, rhsId, linkRole)` SHALL return bool.
6. All methods SHALL throw `RelatedRecordPersistenceException` on storage failure.

---

## FR-RT-001-004 — Exception hierarchy

**Status:** IMPLEMENTED
**Code:** `src/Exception/RelatedRecordException.php`
**Tests:** `tests/Unit/Exception/RelatedRecordExceptionTest.php`

1. `RelatedRecordException` SHALL extend `\RuntimeException`.
2. `UnknownRelatedTypeException` SHALL extend `RelatedRecordException`.
3. `RelatedRecordPersistenceException` SHALL extend `RelatedRecordException`.

---

## FR-RT-001-005 — UI renderer contract

**Status:** IMPLEMENTED
**Code:** `src/UI/RelatedRecordsRendererInterface.php`
**Tests:** `tests/Unit/UI/RelatedRecordsRendererInterfaceTest.php`

1. `renderRelatedRecords(lhsType, lhsId, related, options)` SHALL return string HTML.
2. `$related` SHALL be an array of `RelatedRecordDto`.
3. `$options` SHALL default to an empty array.
