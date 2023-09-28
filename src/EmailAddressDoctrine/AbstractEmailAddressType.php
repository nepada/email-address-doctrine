<?php
declare(strict_types = 1);

namespace Nepada\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\StringType;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddress\InvalidEmailAddressException;

/**
 * @template TEmailAddress of EmailAddress
 */
abstract class AbstractEmailAddressType extends StringType
{

    /**
     * @return class-string<TEmailAddress>
     */
    abstract protected function getEmailAddressClassName(): string;

    public function getName(): string
    {
        return $this->getEmailAddressClassName();
    }

    /**
     * @param EmailAddress|string|null $value
     * @return TEmailAddress|null
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?EmailAddress
    {
        if ($value === null) {
            return $value;
        }

        try {
            return $this->convertToEmailAddress($value);
        } catch (\Throwable $exception) {
            throw ConversionException::conversionFailed((string) $value, $this->getName(), $exception);
        }
    }

    /**
     * @param EmailAddress|string|null $value
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return $value;
        }

        try {
            return $this->convertToEmailAddress($value)->getValue();
        } catch (\Throwable $exception) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', EmailAddress::class, 'email address string'], $exception);
        }
    }

    /**
     * @return TEmailAddress
     * @throws InvalidEmailAddressException
     */
    protected function convertToEmailAddress(EmailAddress|string $value): EmailAddress
    {
        $emailAddressClassName = $this->getEmailAddressClassName();
        if ($value instanceof $emailAddressClassName) {
            return $value;
        }

        if ($value instanceof EmailAddress) {
            $value = $value->toString();
        }

        /** @var TEmailAddress $emailAddress */
        $emailAddress = $emailAddressClassName::fromString($value);

        return $emailAddress;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

}
