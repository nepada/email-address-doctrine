<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Nepada\EmailAddress\CaseInsensitiveEmailAddress;

/**
 * @extends AbstractEmailAddressType<CaseInsensitiveEmailAddress>
 */
class CaseInsensitiveEmailAddressType extends AbstractEmailAddressType
{

    /**
     * @return class-string<CaseInsensitiveEmailAddress>
     */
    protected function getEmailAddressClassName(): string
    {
        return CaseInsensitiveEmailAddress::class;
    }

}
