<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery\MockInterface;
use Nepada\EmailAddress\CaseInsensitiveEmailAddress;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddress\RfcEmailAddress;
use Nepada\EmailAddressDoctrine\EmailAddressLowercaseType;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class EmailAddressLowercaseTypeTest extends TestCase
{

    private EmailAddressLowercaseType $type;

    /**
     * @var AbstractPlatform|MockInterface
     */
    private AbstractPlatform $platform;

    protected function setUp(): void
    {
        parent::setUp();

        if (Type::hasType(EmailAddressLowercaseType::NAME)) {
            Type::overrideType(EmailAddressLowercaseType::NAME, EmailAddressLowercaseType::class);

        } else {
            Type::addType(EmailAddressLowercaseType::NAME, EmailAddressLowercaseType::class);
        }

        $type = Type::getType(EmailAddressLowercaseType::NAME);
        Assert::type(EmailAddressLowercaseType::class, $type);
        $this->type = $type;

        $this->platform = \Mockery::mock(AbstractPlatform::class);
    }

    public function testGetName(): void
    {
        Assert::same('email_address_lowercase', $this->type->getName());
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
            'Could not convert PHP value \'foo\'%a?% to type %S?%email_address_lowercase%S?%. Expected one of the following types: null, Nepada\EmailAddress\EmailAddress, email address string%S?%',
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
     * @return list<mixed[]>
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
                'expected' => 'example@xn--hkyrky-ptac70bc.cz',
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => 'example@xn--hkyrky-ptac70bc.cz',
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
            'Could not convert database value "foo" to Doctrine Type %S?%email_address_lowercase%S?%',
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
            Assert::type(CaseInsensitiveEmailAddress::class, $actual);
            Assert::same((string) $expected, (string) $actual);
        }
    }

    /**
     * @return list<mixed[]>
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


(new EmailAddressLowercaseTypeTest())->run();
