<?php
declare(strict_types = 1);

namespace NepadaTests\EmailAddressDoctrine;

use Nepada\EmailAddress\CaseInsensitiveEmailAddress;
use Nepada\EmailAddress\RfcEmailAddress;
use Nepada\EmailAddressDoctrine\CaseInsensitiveEmailAddressType;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 * @extends EmailAddressTypeTestCase<CaseInsensitiveEmailAddress>
 */
class CaseInsensitiveEmailAddressTypeTest extends EmailAddressTypeTestCase
{

    /**
     * @return class-string<CaseInsensitiveEmailAddressType>
     */
    protected function getEmailAddressTypeClassName(): string
    {
        return CaseInsensitiveEmailAddressType::class;
    }

    /**
     * @return class-string<CaseInsensitiveEmailAddress>
     */
    protected function getEmailAddressClassName(): string
    {
        return CaseInsensitiveEmailAddress::class;
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
                'value' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => 'example@xn--hkyrky-ptac70bc.cz',
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
                'value' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
            [
                'value' => RfcEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
                'expected' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
            [
                'value' => 'Example@HÁČKYČÁRKY.cz',
                'expected' => CaseInsensitiveEmailAddress::fromString('Example@HÁČKYČÁRKY.cz'),
            ],
        ];
    }

}


(new CaseInsensitiveEmailAddressTypeTest())->run();
