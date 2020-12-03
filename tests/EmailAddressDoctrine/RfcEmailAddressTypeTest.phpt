<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Nepada\EmailAddress\CaseInsensitiveEmailAddress;
use Nepada\EmailAddress\RfcEmailAddress;
use Nepada\EmailAddressDoctrine\RfcEmailAddressType;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 * @extends EmailAddressTypeTestCase<RfcEmailAddress>
 */
class RfcEmailAddressTypeTest extends EmailAddressTypeTestCase
{

    /**
     * @return class-string<RfcEmailAddressType>
     */
    protected function getEmailAddressTypeClassName(): string
    {
        return RfcEmailAddressType::class;
    }

    /**
     * @return class-string<RfcEmailAddress>
     */
    protected function getEmailAddressClassName(): string
    {
        return RfcEmailAddress::class;
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
                'value' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => 'Example@xn--hkyrky-ptac70bc.cz',
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => 'Example@xn--hkyrky-ptac70bc.cz',
            ],
        ];
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
                'value' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
        ];
    }

}


(new RfcEmailAddressTypeTest())->run();
