# Test Plan — ksfraser/related-to-core

**Package:** `ksfraser/related-to-core`
**Version:** `1.0.0`

Run with:

```bash
cd /home/kevin/Documents/ksf_related_to_core
vendor/bin/phpunit --no-coverage
```

No DB, no web server, no FA bootstrap. Tests are pure PHPUnit against the DTOs,
exceptions, and interface contracts.

---

## UT-RT-001 — RelatedRecordDto

**File:** `tests/Unit/DTO/RelatedRecordDtoTest.php`
**FR:** FR-RT-001-001

| Test | Asserts |
|------|---------|
| `testConstructionSetsAllProperties` | constructor sets all five properties |
| `testLhsTypeNormalizedToString` | lhs_type cast to string |
| `testRhsTypeNormalizedToString` | rhs_type cast to string |
| `testLinkRoleDefaultsToNull` | omitted link_role is null |
| `testToArrayShape` | returns correct keys |
| `testFromArrayReconstructs` | fromArray rebuilds a DTO |
| `testRoundTrip` | toArray then fromArray equals original |

---

## UT-RT-002 — RelatedQueryDto

**File:** `tests/Unit/DTO/RelatedQueryDtoTest.php`
**FR:** FR-RT-001-002

| Test | Asserts |
|------|---------|
| `testConstructionSetsAllProperties` | constructor sets all five properties |
| `testLhsTypeNormalizedToString` | lhs_type cast to string |
| `testRhsTypeNullWhenOmitted` | rhs_type is null when not provided |
| `testToArrayShape` | returns correct keys |
| `testFromArrayReconstructs` | fromArray rebuilds a DTO |

---

## UT-RT-003 — Repository interface

**File:** `tests/Unit/Repository/RelatedRecordRepositoryInterfaceTest.php`
**FR:** FR-RT-001-003

| Test | Asserts |
|------|---------|
| `testInterfaceDeclaresAllMethods` | relate, unrelate, relatedTo, relatedBy, isRelated all present |
| `testMethodSignatures` | return types and parameter types match contract |

---

## UT-RT-004 — Exception hierarchy

**File:** `tests/Unit/Exception/RelatedRecordExceptionTest.php`
**FR:** FR-RT-001-004

| Test | Asserts |
|------|---------|
| `testRelatedRecordExceptionExtendsRuntimeException` | base exception type |
| `testUnknownRelatedTypeExceptionExtendsBase` | subclass relationship |
| `testRelatedRecordPersistenceExceptionExtendsBase` | subclass relationship |

---

## UT-RT-005 — UI renderer interface

**File:** `tests/Unit/UI/RelatedRecordsRendererInterfaceTest.php`
**FR:** FR-RT-001-005

| Test | Asserts |
|------|---------|
| `testInterfaceDeclaresMethod` | renderRelatedRecords present |
| `testMethodSignature` | parameter and return types match contract |
