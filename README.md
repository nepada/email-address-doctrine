Email address Doctrine type
===========================

[![Build Status](https://travis-ci.org/nepada/email-address-doctrine.svg?branch=master)](https://travis-ci.org/nepada/email-address-doctrine)
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
``` php
\Doctrine\DBAL\Types\Type::addType(
    \Nepada\EmailAddressDoctrine\EmailAddressType::NAME,
    \Nepada\EmailAddressDoctrine\EmailAddressType::class
);
\Doctrine\DBAL\Types\Type::addType(
    \Nepada\EmailAddressDoctrine\EmailAddressLowercaseType::NAME,
    \Nepada\EmailAddressDoctrine\EmailAddressLowercaseType::class
);
```

In Nette with [nettrine/dbal](https://github.com/nettrine/dbal) integration, you can register the types in your configuration:
```yaml
dbal:
    connection:
        types:
            email_address: Nepada\EmailAddressDoctrine\EmailAddressType
            email_address_lowercase: Nepada\EmailAddressDoctrine\EmailAddressLowercaseType
```


Usage
-----

There are two Doctrine types in this package - `EmailAddressType` and `EmailAddressLowercaseType`. Both types map database value to email address value object (see [nepada/email-address](https://github.com/nepada/email-address) for further details) and back. Both types normalize the domain part of the email address before storing it in database, but they differ in handling of the local part of the address.

`EmailAddressType` leaves the local part as is, e.g. `new EmailAddress('ExAmPlE@ExAmPlE.com')` will be stored as string `ExAmPlE@example.com`.

`EmailAddressLowercaseType` converts local part of the address to lowercase before storing it, e.g. `new EmailAddress('ExAmPlE@ExAmPlE.com')` will be stored as string `example@example.com`. This is not RFC 5321 compliant, however in practice all major mail providers treat local part in case insensitive manner.

Example usage in the entity:
``` php
use Doctrine\ORM\Mapping as ORM;
use Nepada\EmailAddress\EmailAddress;

/**
 * @ORM\Entity
 */
class Contact
{

    /** @ORM\Column(type="email_address_lowercase", nullable=false) */
    private EmailAdress $email;

    public function getEmailAddress(): EmailAddress
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
    ->setParameter('emailAddress', 'Example@Example.com', EmailAddressLowercaseType::NAME)
    ->getQuery()
    ->getResult()
```
