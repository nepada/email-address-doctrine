<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Mockery\MockInterface;
use Nepada\EmailAddress\EmailAddress;
use Nepada\EmailAddress\InvalidEmailAddressException;
use Nepada\EmailAddressDoctrine\EmailAddressType;
use NepadaTests\TestCase;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class EmailAddressTypeTest extends TestCase
{

    /** @var EmailAddressType */
    private $type;

    /** @var AbstractPlatform|MockInterface */
    private $platform;

    protected function setUp(): void
    {
        parent::setUp();

        Type::addType(EmailAddressType::NAME, EmailAddressType::class);

        /** @var EmailAddressType $type */
        $type = Type::getType(EmailAddressType::NAME);
        Assert::type(EmailAddressType::class, $type);
        $this->type = $type;

        $this->platform = \Mockery::mock(AbstractPlatform::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Type::overrideType(EmailAddressType::NAME, null);
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
            InvalidEmailAddressException::class
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
                'value' => new EmailAddress('Example@HÁČKYČÁRKY.cz'),
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
            InvalidEmailAddressException::class
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
                'value' => new EmailAddress('Example@HÁČKYČÁRKY.cz'),
                'expected' => new EmailAddress('Example@HÁČKYČÁRKY.cz'),
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => new EmailAddress('Example@HÁČKYČÁRKY.cz'),
            ],
        ];
    }

}


(new EmailAddressTypeTest())->run();
