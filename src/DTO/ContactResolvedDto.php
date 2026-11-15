<?php

declare(strict_types=1);

namespace ksfraser\RelatedTo\DTO;

use ksfraser\RelatedTo\DTO\RelatedRecordDto;

/**
 * Resolved contact record. Default display is First + Last name
 * (Contact::getDisplayName()), optionally suffixed with the contact-relation
 * index in parentheses. Consuming modules may override labels/fields.
 *
 * @BABOK Related: BR-RT-007
 * @package ksfraser\RelatedTo\DTO
 * @since   1.1.0
 */
class ContactResolvedDto extends ResolvedRelatedRecordDto
{
    /**
     * @param RelatedRecordDto $dto          The underlying persistence record
     * @param string|null      $firstName    Person first name
     * @param string|null      $lastName     Person second name
     * @param int|null         $contactIndex Contact-relation index (optional suffix)
     * @param array            $labels       Optional per-field label overrides
     * @BABOK Related: BR-RT-007
     * @since 1.1.0
     */
    public function __construct(
        RelatedRecordDto $dto,
        $firstName = null,
        $lastName = null,
        $contactIndex = null,
        array $labels = array()
    ) {
        $display = trim((string) $firstName);
        $second  = trim((string) $lastName);
        if ($second !== '') {
            $display = $display === '' ? $second : $display . ' ' . $second;
        }
        if ($display === '') {
            $display = $dto->rhsId;
        }
        if ($contactIndex !== null) {
            $display .= ' (' . (int) $contactIndex . ')';
        }

        $fields = array(
            'displayName' => $display,
            'firstName'   => (string) $firstName,
            'lastName'    => (string) $lastName,
        );
        if ($contactIndex !== null) {
            $fields['contactIndex'] = (int) $contactIndex;
        }

        $defaults = array(
            'displayName' => 'Contact',
            'firstName'   => 'First Name',
            'lastName'    => 'Last Name',
        );
        if ($contactIndex !== null) {
            $defaults['contactIndex'] = 'Index';
        }

        parent::__construct($dto, array_merge($defaults, $fields), array_merge($defaults, $labels));
    }

    public function toString()
    {
        return (string) $this->getField('displayName');
    }

    public function toHtml()
    {
        return htmlspecialchars((string) $this->getField('displayName'), ENT_QUOTES, 'UTF-8');
    }
}
