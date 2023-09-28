<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery\MockInterface;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddress\RfcEmailAddress;
use Nepada\EmailAddressDoctrine\EmailAddressType;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class EmailAddressTypeTest extends TestCase
{

    private EmailAddressType $type;

    /**
     * @var AbstractPlatform|MockInterface
     */
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType(EmailAddressType::NAME)) {
            Type::overrideType(EmailAddressType::NAME, EmailAddressType::class);

        } else {
            Type::addType(EmailAddressType::NAME, EmailAddressType::class);
        }

        $type = Type::getType(EmailAddressType::NAME);
        Assert::type(EmailAddressType::class, $type);
        $this->type = $type;

        $this->platform = \Mockery::mock(AbstractPlatform::class);
    }

    public function testGetName(): void
    {
        Assert::same('email_address', $this->type->getName());
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
            'Could not convert PHP value \'foo\'%a?% to type %S?%email_address%S?%. Expected one of the following types: null, Nepada\EmailAddress\EmailAddress, email address string',
        );
    }

    /**
     * @dataProvider getDataForConvertToDatabaseValue
     */
    public function testConvertToDatabaseValueSucceeds(EmailAddress|string|null $value, ?string $expected): void
    {
        Assert::same($expected, $this->type->convertToDatabaseValue($value, $this->platform));
    }

    /**
     * @return mixed[]
     */
    protected function getDataForConvertToDatabaseValue(): array
    {
        return [
            [
                'value' => null,
                'expected' => null,
            ],
            [
                'value' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => 'Example@xn--hkyrky-ptac70bc.cz',
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => 'Example@xn--hkyrky-ptac70bc.cz',
            ],
        ];
    }

    public function testConvertToPHPValueFails(): void
    {
        Assert::exception(
            function (): void {
                $this->type->convertToPHPValue('foo', $this->platform);
            },
            ConversionException::class,
            'Could not convert database value "foo" to Doctrine Type email_address',
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
            Assert::type(RfcEmailAddress::class, $actual);
            Assert::same((string) $expected, (string) $actual);
        }
    }

    /**
     * @return mixed[]
     */
    protected function getDataForConvertToPHPValue(): array
    {
        return [
            [
                'value' => null,
                'expected' => null,
            ],
            [
                'value' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
        ];
    }

}


(new EmailAddressTypeTest())->run();
