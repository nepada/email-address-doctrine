<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Nepada\EmailAddress\RfcEmailAddress;

/**
 * @phpstan-extends AbstractEmailAddressType<RfcEmailAddress>
 */
class RfcEmailAddressType extends AbstractEmailAddressType
{

    /**
     * @phpstan-return class-string<RfcEmailAddress>
     * @return string
     */
    protected function getEmailAddressClassName(): string
    {
        return RfcEmailAddress::class;
    }

}
