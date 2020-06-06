<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Nepada\EmailAddress\EmailAddress;

/**
 * @phpstan-template TEmailAddress of EmailAddress
 */
abstract class AbstractEmailAddressType extends StringType
{

    /**
     * @phpstan-return class-string<TEmailAddress>
     * @return string
     */
    abstract protected function getEmailAddressClassName(): string;

    public function getName(): string
    {
        return $this->getEmailAddressClassName();
    }

    /**
     * @phpstan-return TEmailAddress|null
     * @param EmailAddress|string|null $value
     * @param AbstractPlatform $platform
     * @return EmailAddress|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value === null) {
            return $value;
        }

        try {
            return $this->convertToEmailAddress($value);
        } catch (\Throwable $exception) {
            throw ConversionException::conversionFailed((string) $value, $this->getName());
        }
    }

    /**
     * @param EmailAddress|string|null $value
     * @param AbstractPlatform $platform
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        try {
            return $this->convertToEmailAddress($value)->getValue();
        } catch (\Throwable $exception) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', EmailAddress::class, 'email address string']);
        }
    }

    /**
     * @phpstan-return TEmailAddress
     * @param EmailAddress|string $value
     * @return EmailAddress
     */
    protected function convertToEmailAddress($value): EmailAddress
    {
        $emailAddressClassName = $this->getEmailAddressClassName();
        if ($value instanceof $emailAddressClassName) {
            return $value;
        }

        if ($value instanceof EmailAddress) {
            $value = $value->toString();
        }

        /** @phpstan-var TEmailAddress $emailAddress */
        $emailAddress = $emailAddressClassName::fromString($value);

        return $emailAddress;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
