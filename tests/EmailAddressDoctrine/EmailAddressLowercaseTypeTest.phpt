<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Mockery\MockInterface;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddressDoctrine\EmailAddressLowercaseType;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class EmailAddressLowercaseTypeTest extends TestCase
{

    /** @var EmailAddressLowercaseType */
    private $type;

    /** @var AbstractPlatform|MockInterface */
    private $platform;

    protected function setUp(): void
    {
        parent::setUp();

        Type::addType(EmailAddressLowercaseType::NAME, EmailAddressLowercaseType::class);

        /** @var EmailAddressLowercaseType $type */
        $type = Type::getType(EmailAddressLowercaseType::NAME);
        Assert::type(EmailAddressLowercaseType::class, $type);
        $this->type = $type;

        $this->platform = \Mockery::mock(AbstractPlatform::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Type::overrideType(EmailAddressLowercaseType::NAME, null);
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
            'Could not convert PHP value \'foo\' of type \'string\' to type \'email_address_lowercase\'. Expected one of the following types: null, Nepada\EmailAddress\EmailAddress, email address string'
        );
    }

    /**
     * @dataProvider getDataForConvertToDatabaseValue
     * @param mixed $value
     * @param string|null $expected
     */
    public function testConvertToDatabaseValueSucceeds($value, ?string $expected): void
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
                'value' => EmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
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
            'Could not convert database value "foo" to Doctrine Type email_address_lowercase'
        );
    }

    /**
     * @dataProvider getDataForConvertToPHPValue
     * @param mixed $value
     * @param EmailAddress|null $expected
     */
    public function testConvertToPHPValueSucceeds($value, ?EmailAddress $expected): void
    {
        $actual = $this->type->convertToPHPValue($value, $this->platform);
        if ($expected === null) {
            Assert::null($actual);
        } else {
            Assert::type(EmailAddress::class, $actual);
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
                'value' => EmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => EmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => EmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
        ];
    }

}


(new EmailAddressLowercaseTypeTest())->run();
