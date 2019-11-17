<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Nepada\EmailAddress\EmailAddress;

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
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof EmailAddress) {
            return $value;
        }

        try {
            return EmailAddress::fromString($value);
        } catch (\Throwable $exception) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        if (! $value instanceof EmailAddress) {
            try {
                $value = EmailAddress::fromString($value);
            } catch (\Throwable $exception) {
                throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', EmailAddress::class, 'email address string']);
            }
        }

        return $value->getValue();
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
