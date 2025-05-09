<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery\MockInterface;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddressDoctrine\AbstractEmailAddressType;
use NepadaTests\TestCase;
use Tester\Assert;

/**
 * @template TEmailAddress of EmailAddress
 */
abstract class EmailAddressTypeTestCase extends TestCase
{

    /**
     * @var AbstractEmailAddressType<TEmailAddress>
     */
    protected AbstractEmailAddressType $type;

    /**
     * @var AbstractPlatform|MockInterface
     */
    protected AbstractPlatform $platform;

    /**
     * @return class-string<AbstractEmailAddressType<TEmailAddress>>
     */
    abstract protected function getEmailAddressTypeClassName(): string;

    /**
     * @return class-string<TEmailAddress>
     */
    abstract protected function getEmailAddressClassName(): string;

    /**
     * @return list<mixed[]>
     */
    abstract protected function getDataForConvertToDatabaseValue(): array;

    /**
     * @return list<mixed[]>
     */
    abstract protected function getDataForConvertToPHPValue(): array;

    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType($this->getEmailAddressClassName())) {
            Type::overrideType($this->getEmailAddressClassName(), $this->getEmailAddressTypeClassName());
        } else {
            Type::addType($this->getEmailAddressClassName(), $this->getEmailAddressTypeClassName());
        }

        $type = Type::getType($this->getEmailAddressClassName());
        Assert::type($this->getEmailAddressTypeClassName(), $type);
        $this->type = $type;

        $this->platform = \Mockery::mock(AbstractPlatform::class);
    }

    public function testGetName(): void
    {
        Assert::same($this->getEmailAddressClassName(), $this->type->getName());
    }

    public function testRequiresSQLCommentHint(): void
    {
        Assert::true($this->type->requiresSQLCommentHint($this->platform));
    }

    public function testConvertToDatabaseValueFails(): void
    {
        Assert::exception(
            function (): void {
                $this->type->convertToDatabaseValue('foo', $this->platform);
            },
            ConversionException::class,
            sprintf(
                'Could not convert PHP value \'foo\'%%a?%% to type %%S?%%%s%%S?%%. Expected one of the following types: null, Nepada\EmailAddress\EmailAddress, email address string%%S?%%',
                $this->getEmailAddressClassName(),
            ),
        );
    }

    /**
     * @dataProvider getDataForConvertToDatabaseValue
     */
    public function testConvertToDatabaseValueSucceeds(EmailAddress|string|null $value, ?string $expected): void
    {
        Assert::same($expected, $this->type->convertToDatabaseValue($value, $this->platform));
    }

    public function testConvertToPHPValueFails(): void
    {
        Assert::exception(
            function (): void {
                $this->type->convertToPHPValue('foo', $this->platform);
            },
            ConversionException::class,
            sprintf('Could not convert database value "foo" to Doctrine Type %%S?%%%s%%S?%%', $this->getEmailAddressClassName()),
        );
    }

    /**
     * @dataProvider getDataForConvertToPHPValue
     */
    public function testConvertToPHPValueSucceeds(EmailAddress|string|null $value, ?EmailAddress $expected): void
    {
        $actual = $this->type->convertToPHPValue($value, $this->platform);
        if ($expected === null) {
            Assert::null($actual);
        } else {
            Assert::type($this->getEmailAddressClassName(), $actual);
            Assert::same((string) $expected, (string) $actual);
        }
    }

}
