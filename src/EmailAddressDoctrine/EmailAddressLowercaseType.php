<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

/**
 * @deprecated use CaseInsensitiveEmailAddressType instead
 */
class EmailAddressLowercaseType extends CaseInsensitiveEmailAddressType
{

    /** @final */
    public const NAME = 'email_address_lowercase';

    /**
     * @deprecated Kept for DBAL 3.x compatibility
     */
    public function getName(): string
    {
        return static::NAME;
    }

}
