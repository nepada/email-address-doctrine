Email address Doctrine type
===========================

[![Build Status](https://github.com/nepada/email-address-doctrine/workflows/CI/badge.svg)](https://github.com/nepada/email-address-doctrine/actions?query=workflow%3ACI+branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/nepada/email-address-doctrine/badge.svg?branch=master)](https://coveralls.io/github/nepada/email-address-doctrine?branch=master)
[![Downloads this Month](https://img.shields.io/packagist/dm/nepada/email-address-doctrine.svg)](https://packagist.org/packages/nepada/email-address-doctrine)
[![Latest stable](https://img.shields.io/packagist/v/nepada/email-address-doctrine.svg)](https://packagist.org/packages/nepada/email-address-doctrine)


Installation
------------

Via Composer:

```sh
$ composer require nepada/email-address-doctrine
```

Register the types in your bootstrap:
```php
\Doctrine\DBAL\Types\Type::addType(
    \Nepada\EmailAddress\RfcEmailAddress::class,
    \Nepada\EmailAddressDoctrine\RfcEmailAddressType::class
);
\Doctrine\DBAL\Types\Type::addType(
    \Nepada\EmailAddress\CaseInsensitiveEmailAddress::class,
    \Nepada\EmailAddressDoctrine\CaseInsensitiveEmailAddressType::class
);
```

In Nette with [nettrine/dbal](https://github.com/nettrine/dbal) integration, you can register the types in your configuration:
```yaml
dbal:
    connection:
        types:
            Nepada\EmailAddress\RfcEmailAddress: Nepada\EmailAddressDoctrine\RfcEmailAddressType
            Nepada\EmailAddress\CaseInsensitiveEmailAddress: Nepada\EmailAddressDoctrine\CaseInsensitiveEmailAddressType
```


Usage
-----

This package provides two Doctrine types:
1) `RfcEmailAddressType` for storing emails represented by `RfcEmailAddress`.
2) `CaseInsensitiveEmailAddressType` for storing emails represented by `CaseInsensitiveEmailAddress`.

 Both types normalize the domain part of the email address before storing it in database, but they differ in handling of the local part of the address. See [nepada/email-address](https://github.com/nepada/email-address) for further details.

Example usage in the entity:
```php
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Nepada\EmailAddress\CaseInsensitiveEmailAddress;

#[Entity]
class Contact
{

    #[Column(type: CaseInsensitiveEmailAddress::class, nullable: false)]
    private CaseInsensitiveEmailAddress $email;

    public function getEmailAddress(): CaseInsensitiveEmailAddress
    {
        return $this->emailAddress;
    }

}
```

Example usage in query builder:
```php
$result = $repository->createQueryBuilder('foo')
    ->select('foo')
    ->where('foo.email = :emailAddress')
     // the parameter value is automatically normalized to example@example.com
    ->setParameter('emailAddress', 'Example@Example.com', CaseInsensitiveEmailAddress::class)
    ->getQuery()
    ->getResult();
```
