# Error handling middleware

[![Total Downloads]](https://packagist.org/packages/lcobucci/error-handling-middleware)
[![Latest Stable Version]](https://packagist.org/packages/lcobucci/error-handling-middleware)
[![Unstable Version]](https://packagist.org/packages/lcobucci/error-handling-middleware)

[![Build Status]](https://github.com/lcobucci/error-handling-middleware/actions?query=workflow%3A%22PHPUnit%20Tests%22+branch%3A1.2.x)
[![Code Coverage]](https://codecov.io/gh/lcobucci/error-handling-middleware)

## Motivation

There are many PHP implementations for the [RFC 7807](https://tools.ietf.org/html/rfc7807),
even providing [PSR-15 middleware](https://www.php-fig.org/psr/psr-15/). However,
most of them - if not all - mix content negotiation, logging, and formatting with
error handling. Some even force you to throw specific types of exceptions in order
for them to work.

I believe that those aren't the best design decisions and that we need more
flexibility to solve this problem.

## Installation

This package is available on [Packagist], and we recommend you to install it using [Composer]:

```shell
composer require lcobucci/error-handling-middleware
```

## Usage

In order to us this package you must add the middleware to your pipeline, configuring
the desired behaviour (debug info strategy and status code extraction strategy).

Once this is set you'll be able to have your errors/exceptions converted into the
correct HTTP responses.

### Middleware position

This package provides two middleware for handling errors: error logging and error
conversion.

They are designed to be used in the very beginning of the HTTP middleware pipeline,
just after the [content negotiation](https://github.com/lcobucci/content-negotiation-middleware) one:

```php
<?php
use Lcobucci\ContentNegotiation\ContentTypeMiddleware;
use Lcobucci\ErrorHandling\ErrorConversionMiddleware;
use Lcobucci\ErrorHandling\ErrorLoggingMiddleware;

// In a Laminas Mezzio application, it would look like this:
$application->pipe(ContentTypeMiddleware::fromRecommendedSettings( /* ... */ )); // Very first middleware
$application->pipe(new ErrorConversionMiddleware( /* ... */ ));
$application->pipe(new ErrorLoggingMiddleware( /* ... */ ));

// all other middleware.
```

With that we'll be able to perform the logging and conversion in the correct order,
delegating the content negotiation and formatting to `ContentTypeMiddleware` - using
the configured formatters.

#### Important

The `ErrorConversionMiddleware` uses an `UnformattedResponse` to let the
`ContentTypeMiddleware` perform the formatting. Make sure you have configured
formatters for the MIME types `application/problem+json` and/or
`application/problem+xml`.

It also makes the error/exception available in the `error` attribute of the response,
so you may access it (if needed) by using another middleware between
`ErrorConversionMiddleware` and `ContentTypeMiddleware`.

### Configuring the conversion middleware behaviour

There're two extension points that you can use for that: debug info strategy and
status code extraction strategy.

You can also configure the response body attributes by implementing certain interfaces
in your exceptions.

#### Debug info strategy

This defines how the `_debug` property should be generated in the response body.
We provide two default implementations - one designed for production mode and the
other for development mode.

To configure this you must pass the desired implementation (or a customised one) as
the second argument of the `ErrorConversionMiddleware` constructor.

To provide your own implementation you need to create a class that implements the
`DebugInfoStrategy` interface.

#### Status code extraction strategy

This defines how the translation from error/exception to HTTP status code should
be done. We provide a single default implementation for that, which is based on
class maps.

To configure this you must pass the desired implementation (or a customised one) as
the third argument of the `ErrorConversionMiddleware` constructor.

To provide your own implementation you need to create a class that implements the
`StatusCodeExtractionStrategy` interface.

##### Default class map

The default map uses the marker interfaces in this packages to perform such translation.
If the error/exception doesn't implement any of the marker interfaces, the error/exception
code will be used (when it's different than zero), or fallback to the status code
500 (Internal Server Error).

The default map is:

* `Lcobucci\ErrorHandling\Problem\InvalidRequest` -> `400`
* `Lcobucci\ErrorHandling\Problem\AuthorizationRequired` -> `401`
* `Lcobucci\ErrorHandling\Problem\Forbidden` -> `403`
* `Lcobucci\ErrorHandling\Problem\ResourceNotFound` -> `404`
* `Lcobucci\ErrorHandling\Problem\Conflict` -> `409`
* `Lcobucci\ErrorHandling\Problem\ResourceNoLongerAvailable` -> `410`
* `Lcobucci\ErrorHandling\Problem\UnprocessableRequest` -> `422`
* `Lcobucci\ErrorHandling\Problem\ServiceUnavailable`-> `503`

This allows us to create our own exceptions that are automatically converted to the
correct status code:

```php
<?php
declare(strict_types=1);

namespace My\Fancy\App\UserManagement;

use Lcobucci\ErrorHandling\Problem\ResourceNotFound;
use RuntimeException;

final class UserNotFound extends RuntimeException implements ResourceNotFound
{
}
```

**Important**: you SHOULD NOT implement more than one of the marker interfaces,
otherwise you may have unexpected results.

#### Customising the response body properties

With this library, you may modify the `type` and `title` properties of the generated
response and also append new members to it.

That's done by implementing the `Typed`, `Titled`, and/or `Detailed` interfaces -
you don't necessarily need to implement all of them, only the ones you want.

The example below shows how to represent one of the samples in the
[RFC 7807](https://tools.ietf.org/html/rfc7807#section-3):

```php
<?php
declare(strict_types=1);

namespace My\Fancy\App\UserManagement;

use Lcobucci\ErrorHandling\Problem\Forbidden;
use Lcobucci\ErrorHandling\Problem\Typed;
use Lcobucci\ErrorHandling\Problem\Titled;
use Lcobucci\ErrorHandling\Problem\Detailed;
use RuntimeException;
use function sprintf;

final class InsufficientCredit extends RuntimeException implements Forbidden, Typed, Titled, Detailed
{
    private $currentBalance;

    public static function forPurchase(int $currentBalance, int $cost): self
    {
        $exception = new self(sprintf('Your current balance is %d, but that costs %d.', $currentBalance, $cost));
        $exception->currentBalance = $currentBalance;

        return $exception;
    }

    public function getTypeUri(): string
    {
        return 'https://example.com/probs/out-of-credit';
    }

    public function getTitle(): string
    {
        return 'You do not have enough credit.';
    }

    /** @inheritDoc */
    public function getExtraDetails(): array
    {
        return ['balance' => $this->currentBalance]; // you might add "instance" and "accounts" too :)
    }
}
```

## License

MIT, see [LICENSE].

[Total Downloads]: https://img.shields.io/packagist/dt/lcobucci/error-handling-middleware.svg?style=flat-square
[Latest Stable Version]: https://img.shields.io/packagist/v/lcobucci/error-handling-middleware.svg?style=flat-square
[Unstable Version]: https://img.shields.io/packagist/vpre/lcobucci/error-handling-middleware.svg?style=flat-square
[Build Status]: https://img.shields.io/github/workflow/status/lcobucci/error-handling-middleware/PHPUnit%20tests/1.2.x?style=flat-square
[Code Coverage]: https://codecov.io/gh/lcobucci/error-handling-middleware/branch/1.2.x/graph/badge.svg
[Packagist]: http://packagist.org/packages/lcobucci/error-handling-middleware
[Composer]: http://getcomposer.org
[LICENSE]: LICENSE

