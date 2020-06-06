<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

/**
 * @deprecated
 */
class EmailAddressLowercaseType extends CaseInsensitiveEmailAddressType
{

    public const NAME = 'email_address_lowercase';

    public function getName(): string
    {
        return static::NAME;
    }

}
