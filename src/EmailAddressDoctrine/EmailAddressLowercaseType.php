<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Nepada\EmailAddress\EmailAddress;

class EmailAddressLowercaseType extends EmailAddressType
{

    public const NAME = 'email_address_lowercase';

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

        if (!$value instanceof EmailAddress) {
            try {
                $value = EmailAddress::fromString($value);
            } catch (\Throwable $exception) {
                throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', EmailAddress::class, 'email address string']);
            }
        }

        return $value->getLowercaseValue();
    }

}
