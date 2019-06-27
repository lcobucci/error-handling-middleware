# Error handling middleware

[![Total Downloads](https://img.shields.io/packagist/dt/lcobucci/error-handling-middleware.svg?style=flat-square)](https://packagist.org/packages/lcobucci/error-handling-middleware)
[![Latest Stable Version](https://img.shields.io/packagist/v/lcobucci/error-handling-middleware.svg?style=flat-square)](https://packagist.org/packages/lcobucci/error-handling-middleware)
[![Unstable Version](https://img.shields.io/packagist/vpre/lcobucci/error-handling-middleware.svg?style=flat-square)](https://packagist.org/packages/lcobucci/error-handling-middleware)

![Branch master](https://img.shields.io/badge/branch-master-brightgreen.svg?style=flat-square)
[![Build Status](https://img.shields.io/travis/lcobucci/error-handling-middleware/master.svg?style=flat-square)](http://travis-ci.org/lcobucci/error-handling-middleware)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/lcobucci/error-handling-middleware/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/error-handling-middleware/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/lcobucci/error-handling-middleware/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/lcobucci/error-handling-middleware/?branch=master)

## Motivation

There are many PHP implementations for the [RFC 7807](https://tools.ietf.org/html/rfc7807),
even providing [PSR-15 middleware](https://www.php-fig.org/psr/psr-15/). However,
most of them - if not all - mix content negotiation, logging, and formatting with
error handling. Some even force you to throw specific types of exceptions in order
for them to work.

I believe that those aren't the best design decisions and that we need more
flexibility to solve this problem.

## Installation

This package is available on [Packagist](https://packagist.org/packages/lcobucci/error-handling-middleware),
and we recommend you to install it using [Composer](https://getcomposer.org):

```shell
composer require lcobucci/error-handling-middleware
```

## Usage

TDB

## License

MIT, see [LICENSE file](LICENSE).

