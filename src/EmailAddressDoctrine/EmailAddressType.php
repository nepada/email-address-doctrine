<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

/**
 * @deprecated use RfcEmailAddressType instead
 */
class EmailAddressType extends RfcEmailAddressType
{

    public const NAME = 'email_address';

    public function getName(): string
    {
        return static::NAME;
    }

}
