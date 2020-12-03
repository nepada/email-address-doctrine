<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Nepada\EmailAddress\RfcEmailAddress;

/**
 * @extends AbstractEmailAddressType<RfcEmailAddress>
 */
class RfcEmailAddressType extends AbstractEmailAddressType
{

    /**
     * @return class-string<RfcEmailAddress>
     */
    protected function getEmailAddressClassName(): string
    {
        return RfcEmailAddress::class;
    }

}
