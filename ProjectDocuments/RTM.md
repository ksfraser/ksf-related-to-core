# RTM — ksfraser/related-to-core

**Package:** `ksfraser/related-to-core`
**Version:** `1.0.0`
**Requirement definitions:** `ProjectDocuments/Requirements.md`

| BR | Requirement | Implementation | Tests | Status |
|----|-------------|----------------|-------|--------|
| BR-RT-001 | Generic related-record association | `DTO/RelatedRecordDto`, `DTO/RelatedQueryDto`; `UI/RelateRecordsBar` | DTO tests | IMPLEMENTED |
| BR-RT-002 | Repository contract | `Repository/RelatedRecordRepositoryInterface` | `tests/Unit/Repository/RelatedRecordRepositoryInterfaceTest.php` | IMPLEMENTED |
| BR-RT-003 | DTOs | `DTO/RelatedRecordDto` (`toArray`/`fromArray`), `DTO/RelatedQueryDto` (`toArray`/`fromArray`) | `tests/Unit/DTO/RelatedRecordDtoTest.php`, `tests/Unit/DTO/RelatedQueryDtoTest.php` | IMPLEMENTED |
| BR-RT-004 | Exceptions | `Exception/RelatedRecordException`, `UnknownRelatedTypeException`, `RelatedRecordPersistenceException` | `tests/Unit/Exception/RelatedRecordExceptionTest.php` | IMPLEMENTED |
| BR-RT-005 | UI renderer contract | `UI/RelatedRecordsRendererInterface`, `UI/DefaultRelatedRecordsRenderer` | `tests/Unit/UI/RelatedRecordsRendererInterfaceTest.php` | IMPLEMENTED |
| BR-RT-006 | Entity type registry | External: `ksfraser\FrontAccounting\Common\Repository\EntityRegistry` (hosted in ksf_FA_Common); core package consumes type keys only | — | IMPLEMENTED (external registry) |
| BR-RT-007 | Resolved display DTOs | `DTO/ResolvedRelatedRecordDto` + 13 concrete subclasses (`ContactResolvedDto`, `CustomerResolvedDto`, `InvoiceResolvedDto`, `ItemResolvedDto`, `SupplierResolvedDto`, `SalesOrderResolvedDto`, `PurchaseOrderResolvedDto`, `MeetingResolvedDto`, `OpportunityResolvedDto`, `ProjectResolvedDto`, `TaskResolvedDto`, `CalendarResolvedDto`, `AttachmentResolvedDto`) | `tests/Unit/DTO/ResolvedDtoTest.php` | IMPLEMENTED |
| BR-RT-008 | RELATED_TO_FIELDS hook | External: `ksf_FA_RelatedTo/hooks.php` `RELATED_TO_FIELDS` responder | ksf_FA_RelatedTo `tests/Unit/HooksTest.php` | IMPLEMENTED (consumer contract) |

## `@BABOK` annotation index

| Annotation | Found in |
|------------|----------|
| BR-RT-001 | `DTO/RelatedRecordDto.php`, `DTO/RelatedQueryDto.php`, `UI/RelateRecordsBar.php` |
| BR-RT-002 | `Repository/RelatedRecordRepositoryInterface.php` |
| BR-RT-003 | `DTO/RelatedRecordDto.php`, `DTO/RelatedQueryDto.php` |
| BR-RT-004 | `Exception/RelatedRecordException.php` |
| BR-RT-005 | `UI/RelatedRecordsRendererInterface.php`, `UI/DefaultRelatedRecordsRenderer.php` |
| BR-RT-007 | `DTO/ResolvedRelatedRecordDto.php` and all 13 resolved DTO subclasses |
| BR-RT-006, BR-RT-008 | Implemented outside this package (ksf_FA_Common EntityRegistry; ksf_FA_RelatedTo hooks.php) |
