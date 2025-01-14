<?php
declare(strict_types = 1);

use Doctrine\DBAL\Types\Exception\ValueNotConvertible;

$config = ['parameters' => ['ignoreErrors' => []]];

if (class_exists(ValueNotConvertible::class)) { // DBAL 3.x compatibility
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined static method Doctrine\\\\DBAL\\\\Types\\\\ConversionException\\:\\:conversionFailed\\(\\)\\.$#',
        'path' => __DIR__ . '/../../src/EmailAddressDoctrine/AbstractEmailAddressType.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined static method Doctrine\\\\DBAL\\\\Types\\\\ConversionException\\:\\:conversionFailedInvalidType\\(\\)\\.$#',
        'path' => __DIR__ . '/../../src/EmailAddressDoctrine/AbstractEmailAddressType.php',
        'count' => 1,
    ];
}

$config['parameters']['ignoreErrors'][] = [
    'message' => '#^Invalid type mixed to throw\\.$#',
    'path' => __DIR__ . '/../../src/EmailAddressDoctrine/AbstractEmailAddressType.php',
    'count' => 2,
];

return $config;
