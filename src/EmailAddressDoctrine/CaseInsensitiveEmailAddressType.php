<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Nepada\EmailAddress\CaseInsensitiveEmailAddress;

/**
 * @phpstan-extends AbstractEmailAddressType<CaseInsensitiveEmailAddress>
 */
class CaseInsensitiveEmailAddressType extends AbstractEmailAddressType
{

    /**
     * @phpstan-return class-string<CaseInsensitiveEmailAddress>
     * @return string
     */
    protected function getEmailAddressClassName(): string
    {
        return CaseInsensitiveEmailAddress::class;
    }

}
