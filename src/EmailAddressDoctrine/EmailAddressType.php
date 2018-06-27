<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddress\InvalidEmailAddressException;

class EmailAddressType extends StringType
{

    public const NAME = 'email_address';

    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return EmailAddress|null
     * @throws InvalidEmailAddressException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof EmailAddress) {
            return $value;
        }

        return new EmailAddress($value);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string|null
     * @throws InvalidEmailAddressException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        if (!$value instanceof EmailAddress) {
            $value = new EmailAddress($value);
        }

        return $value->getValue();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
