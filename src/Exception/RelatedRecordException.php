<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\Exception;

/**
 * Base exception for the related-to core package.
 *
 * @since 1.0.0
 */
class RelatedRecordException extends \RuntimeException
{
}

/**
 * Thrown when a related-record type is not registered or unknown.
 *
 * @since 1.0.0
 */
class UnknownRelatedTypeException extends RelatedRecordException
{
}

/**
 * Thrown when the repository layer cannot persist or fetch related records.
 *
 * @since 1.0.0
 */
class RelatedRecordPersistenceException extends RelatedRecordException
{
}
