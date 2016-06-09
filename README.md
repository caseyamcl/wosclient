# Client for DDN Web Object Scalar (WOS) HTTP API

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This library is a portable HTTP client for the [DDN Web Object Scalar](http://www.ddn.com/products/object-storage-web-object-scaler-wos/)
storage system HTTP API.

Unlike the official DDN PHP client, this library communicates with the WOS device
over HTTP, and does not require the installation of any PHP C extensions.

## Install

This library requires PHP v5.5 or newer.  It has been tested with PHP7.
If you wish to run tests, you must run v5.6 or newer.

I also recommend installing the `ext-curl` PHP extension.

Via Composer

``` bash
$ composer require caseyamcl/wosclient guzzlehttp/guzzle
```

*Note*: By default, this library uses [Guzzle v6.0](http://docs.guzzlephp.org/en/latest/index.html).
However, if you do not wish to use Guzzle 6, you can create your own implementation by implementing
the `WosClientInterface` yourself (details below).

## Usage

This library uses [PSR-4 autoloading](http://www.php-fig.org/psr/psr-4/).
If you are not using Composer or another PSR-4 autoloader, you will need to manually include all 
of the files in the `src/` directory (but I highly discourage this; use an autoloader!).

If you know the URL to your WOS API and your WOS Policy name or ID, you
can create a `WosClient` instance by calling the `WosClient::build()` constructor.
Pass in the URL to your WOS API and your WOS Policy name or ID:

``` php

use WosClient\WosClient;

$wosClient = WosClient::build('http://mywos.example.org/', 'my-policy-id');

```

The `WosClient` contains four public methods for interacting with the object storage API:

```php

// Get an object by its Object ID (OID)
$wosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345');

// Get a range of data for a large object (get bytes 50000-100000)
$partialWosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345', '50000-100000'); 

// Get metadata for an Object ID (OID)
$wosMetadata = $wosClient->getMetadata('abcdef-ghijkl-mnopqr-12345');

// Put an object
$httpResponse = $wosClient->putObject('some-serializable-or-streamable-data', ['some' => 'metadata']);

// Reserve an Object ID, without putting any data in it yet
$reservedObjectId = $wosClient->reserveObject();

// Put an object having reserved its ID ahead of time with reserveObject()
$httpResponse = $wosClient->putObject('some-serializable-or-streamable-data', [], $reservedObjectId);

// Delete an object
$httpResponse = $wosClient->deleteObject('abcdef-ghijkl-mnopqr-12345');

```

All four methods optionally accept an array of 
[Guzzle HTTP request options](http://docs.guzzlephp.org/en/latest/request-options.html)
as the last method parameter.  If you pass any options in this way, they
will override all default and computed request options.  If you pass in
HTTP headers in this way, they will be merged with the default headers 
(see [Guzzle Docs](http://docs.guzzlephp.org/en/latest/request-options.html#headers)).

### Using responses

The `WosClient::getObject()` method returns an instance of `WosClient\WosObject`:

```php

// Get the object
$wosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345');

// Get the data from the response as a string
$wosObject->getData()->__toString();

// or, as a shortcut..
$wosObject->__toString();

// Get the Object ID
$wosObject->getId();
```

The `WosClient::getMetadata()` and the `WosObject::getMetadata()` methods return an instance of
`WosClient\WosObjectMetadata`:

```php

// Get the meta-data (instance of WosObjectMetadata; see below)
$metadata = $wosObject->getMetadata();

// Get access to the HTTP response
$wosObject->getHttpResponse();

// Get the metadata from the object
$wosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345');
$metadata = $wosObject->getMetadata();

// ..or just get the metadata for the object from the WOS server without
// getting the content
$metadata = $wosClient->getMetadata('abcdef-ghijkl-mnopqr-12345');

// Get the object ID
$objectId = $metadata->getObjectId();

// Get the object size in bytes - This returns NULL if not known
$numBytes = $metadata->getObjectSize();

// Access custom metadata (having been added with `WosObject::putMetadata()`
$foo = $metadata->get('foo');

// Conditionally get metadat if it exists
if  ($metadata->has('bar')) {
    $bar = $metadata->get('bar');
}

// Metadata implements \Countable, \ArrayAccess, and \Traversable
$foo = $metadata['foo'];
$bar = $metadata['bar'];
$num = count($metadata);

for ($metadata as $key => $val) {
    echo "$key: $val";
}

```

The `WosClient::putObject()` and `WosClient::reserveObject()` methods return an instance of `WosClient\WosObjectId`:

```php

// Put an object with auto-generated ID
$wosObjectId = $wosClient->putObject('some object data');

// Reserve an object ID
$wosObjectId = $wosClient->reserveObject();

// Get the ID as a string
$idString = $wosObjectId->getId();

// ..or cast as string..
$idString = (string) $wosObjectId;

```

### Streaming large objects

The WOS supports objects up to 5 terabytes in size!

If the object you are retrieving from the WOS server is very large, it is
not a good idea to read the entire thing into memory at once.

Fortunately, by default, this library will stream data from the WOS server,
instead of downloading it into memory.  The library uses the PSR-7 
[`StreamableInterface`](http://www.php-fig.org/psr/psr-7/#1-3-streams) to
accomplish this.

To stream a large file, simply seek through it rather than converting it
to a string:

```php

$veryLargeWosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345');
$dataStream = $veryLargeWosObject->getData();

// Read the data stream 1024 bytes at a time, and 
// echo the data out..  You could do anything with the chunked data if
// you wish...
while ( ! $dataStream->eof()) {
    echo $dataStream->read(1024);
}

```

Another way to stream data from your WOS server is to specify the `$range` 
parameter of the `WosClient::getObject()` to retrieve only chunks of the 
large object at a time:

```php

$metadata  = $wosClient->getMetadata('abcdef-ghijkl-mnopqr-12345');
$chunkSize = 1024; // read this many bytes at a time

for ($i = 0; $i < $metadata->getLength(); $i+= $chunkSize) {

    $from = $i;
    $to   = $i + $chunkSize;

    // WosClient::getObject second parameter accepts range in the format '####-####' (e.g. '1024-2048')
    echo $wosClient->getObject('abcdef-ghijkl-mnopqr-12345', $from . '- . $to)->__toString();
}

```

### Handling errors

This library converts all application-layer runtime errors into instances of
`WosClient\Exception\WosException`.  There are three sub-classes:

* `WosClient\Exception\WosServerException` - This exception is thrown
  when the WOS server rejects the request or encounters an error and returns
  a response with a `x-ddn-status` header other than success (0).  The
  exception code will correspond to the WOS DDN Status code, and the message
  will be a detailed description of the code that was returned from the server.
* `WosClient\Exception\InvalidResponseException` - This exception
  is thrown when a HTTP response from the server is missing a HTTP header
  that is expected to be present.  E.g. when a `getObject()` sever response
  does not include a `x-ddn-oid` header.
* `WosClient\Exception\InvalidParameterException` - This exception is
  thrown when a provided header value is not in the expected format that
  the WOS requires.  For example, if the `Range` header is not in `###-###`
  format.  It is thrown before the request is sent to the server.

Example:

```php

use WosClient\Exception\WosServerException;

try {
   $wosClient->getObject('does-not-exist');
} catch (WosServerException $e) {

    // WOS User-friendly message, e.g., 'Object cannot be located'
    echo $e->getMessage();
    
    // WOS Code, e.g., 207
    echo $e->getCode();
    
    // WOS Error Name (machine-friendly, uses CamelCase), e.g., 'ObjNotFound'
    echo $e->getErrorName();
}

```

Note that `WosServerException` is ONLY thrown when the server emits a response with
the `x-ddn-status` header present, and the header is a non-zero value.

It is NOT thrown in the event of any other HTTP transmission error (such as network timeout, or an
internal WOS server error).  You can catch these types of HTTP errors separately, by catching
Guzzle exceptions:

```php

use WosClient\Exception\WosServerException;
use WosClient\Exception\WosRequestException;
use GuzzleHttp\Exception\GuzzleException;

try {
   $wosClient->getObject('does-not-exist');
}
catch (WosServerException $e) {

    // WOS Exception thrown by the WOS Client
    echo 'WOS Error: ' . $e->getMessage();
    
}
catch (WosRequestException $e) {

    // Some other application exception occurred, such as
    // the WOS server returned a response that is missing an
    // expected header (this really should never happen)
    echo 'Something strange happened: ' . $e->getMessage();
}
catch (GuzzleException $e) {

   // HTTP Exception thrown by Guzzle
   echo 'HTTP Error: ' . $e->getMessage();
}

```

The Guzzle library contains a number of different exception classes for specific
error cases, in case you wish to be more specific about your exception handling.

### Instantiating with a custom Guzzle 6 client instance

You may wish to use your own Guzzle 6 Client instance to make requests to the WOS server.
Some examples of why you may wish to do this include:

* you have setup your own request/response middleware, or 
* you wish to use custom HTTP request default values (such as `connect_timeout`), or
* you wish to gain access to HTTP `Response` objects during the request/response cycle.

To use a custom Guzzle client instance, simply use the main constructor 
for the `WosClient\WosClient` class instead of the `build()` constructor. 

The `base_uri` parameter **MUST** be set in your Guzzle client class, or the library will
throw a `\RuntimeException` during object construction.  This value must be the URL for 
one of your WOS nodes.

You also may wish to set the `x-ddn-policy` header, so that you do not need to
specify it in each request:

```php

use WosClient\WosClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;

// Setup custom Guzzle client
$guzzleClient = new GuzzleClient([
    'base_uri'        => 'http://mywos.example.org/',
    'connect_timeout' => 60,
    'handler'         => HandlerStack::create(new CurlHandler()),
    'headers'         => [
        'x-ddn-policy' => 'my-policy'
    ],
    /** ..other guzzle options here.. */
]);

// Instantiate a new WOS Client
$wosClient = new WosClient($guzzleClient);
```

### Creating a different WOS Client implementation

You may wish to write your own implementation for the interfaces included
in this library.  The only dependency in this case is that you must include
the `"psr/http-message": "~1.0"` package.

If your implementation uses a [PSR-7 compliant](http://www.php-fig.org/psr/psr-7/)
HTTP library, you only need to implement the `WosClient\WosClientInterface`.  You can use the
built-in implementations of all other classes.

If, however, your implementation does NOT implement PSR-7, you will
need to implement the following interfaces:

* `WosClient\WosClientInterface`
* `WosObjectInterface`
* `WosObjectIdInterface`
* `WosObjectMetadataInterface`
* `Psr\Http\Message\StreamInterface` (hint: [Guzzle Streams](https://github.com/guzzle/streams) does this pretty well)

Each interface file contains pretty good documentation for how its methods should behave.

Note that you should only throw exceptions in specific cases:

* `WosClient\Exception\WosServerException` - Throw this if the WOS server emits an error code (refer to the WOS API documentation).
* `WosClient\Exception\InvalidParameterException` - Throw this if you validate parameters or HTTP headers on the
  client-side, before sending the request to the server.
* `WosClient\Exception\InvalidResponseException` - Throw this if the server generates a response that the client does
  not know how to process.  For example, the server does not include a HTTP header that is expected to exist.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Testing

To run tests, be sure that you have installed all of the dependencies 
in the `require-dev` portion of the `composer.json` file.

Run unit tests:

``` bash
$ composer test
```

This library also includes a simple console utility to test the client
against your own WOS device.  The test suite writes two tiny objects
to the WOS and then deletes them:

```bash
$ composer livetest http://your-wos.example.org your-policy-id
```

Run the PHP CodeSniffer to detect style errors:

```bash
$ composer sniff
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email caseyamcl@gmail.com instead of using the issue tracker.

## Credits

- [Casey McLaughlin][link-author]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/caseyamcl/wosclient.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/caseyamcl/wosclient/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/caseyamcl/wosclient.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/caseyamcl/wosclient.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/caseyamcl/wosclient.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/caseyamcl/wosclient
[link-travis]: https://travis-ci.org/caseyamcl/wosclient
[link-scrutinizer]: https://scrutinizer-ci.com/g/caseyamcl/wosclient/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/caseyamcl/wosclient
[link-downloads]: https://packagist.org/packages/caseyamcl/wosclient
[link-author]: https://github.com/caseyamcl
