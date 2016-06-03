# Web Object Scalar (WOS) Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

This library is a portable REST client for the [DDN Web Object Scalar](http://www.ddn.com/products/object-storage-web-object-scaler-wos/)
storage system.

Unlike the official DDN PHP solution, this library does not require the installation of
any PHP extensions.  This library uses the HTTP WOS API.

## Install

This library requires PHP v5.5 or newer.

Via Composer

``` bash
$ composer require caseyamcl/wosclient guzzlehttp/guzzle
```

*Note*: For default usage, this library requires [Guzzle v6.0](http://docs.guzzlephp.org/en/latest/index.html).
However, if you do not wish to use Guzzle 6, you can create your own implementation by only installing
the `caseyamcl/wosclient` package and implementing `WosClientInterface` yourself.

## Usage

This library uses [PSR-4 autoloading](http://www.php-fig.org/psr/psr-4/).
If you are not using Composer or another PSR-4 autoloader, you will need to require all 
of the files  in the `src/` directory (but I highly discourage this; use an autoloader!).

The simplest way to create the client is to use the `WosClient::build()` constructor.  Pass
in the URL to your WOS API and your WOS Policy name or ID.

``` php

use WosClient\WosClient;

$wosClient = WosClient::build('http://mywos.example.org/', 'my-policy-id');

```

The `WosClient` contains four methods:

```

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

You can pass in custom options for any request.  The last parameter accepted in each of the above methods
is an array of [Guzzle HTTP request options](http://docs.guzzlephp.org/en/latest/request-options.html).  These will
override the defaults set in the library, or specified during construction of the service.

### Using responses

State-changing operations (`putObject` and `deleteObject`) will simply return a PSR-7 HTTP response, which you should
not need to use in any way, unless you wish to do something with them.

The `WosClient::getObject()` method will return an instance of `WosObject`:

```php

// Get the object
$wosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345');

// Get the data from the response as a string
$wosObject->getData()->__toString();
// or, as a shortcut..
$wosObject->__toString();

// Get the Object ID
$wosObject->getId();

// Get the meta-data (instance of WosObjectMetadata; see below)
$metadata = $wosObject->getMetadata();

// Get access to the HTTP response
$wosObject->getHttpResponse();
```


The `WosClient::getMetadata()` and the `WosObject::getMetadata()` methods will return an instance of
`WosObjectMetadata`:

```php

// Get the metadata from the object
$wosObject = $wosClient->getObject('abcdef-ghijkl-mnopqr-12345');
$metadata = $wosObject->getMetadata();

// ..or just get the metadata for the object from the WOS server without
// getting the content
$metadata = $wosClient->getMetadata('abcdef-ghijkl-mnopqr-12345');


// Get the object ID
$objectId = $metadata->getObjectId();

// Get the object length (size in bytes)
$numBytes = $metadata->getLength();

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

// You can also get the raw HTTP response from the metadata
$httpResponse = $metadata->getHttpResponse();
```

### Streaming large objects

If the object you are retrieving from the WOS server is very large, it is
probably not a good idea to read the entire thing into memory at once.

Fortunately, by default, this library will stream data from the WOS server,
instead of downloading it into memory.  To accomplish this, the library uses
the PSR-7 [`StreamableInterface`](http://www.php-fig.org/psr/psr-7/#1-3-streams).  

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

Another option is to use the `$range` parameter of the `WosClient::getObject()`
to retrieve only chunks of the large object at a time:

```php

$metadata  = $wosClient->getMetadata('abcdef-ghijkl-mnopqr-12345');
$chunkSize = 1024; // read this many bytes at a time

for ($i = 0; $i < $metadata->getLength(); $i+= $chunkSize) {

    $from = $i;
    $to   = $i + $chunkSize;

    echo $wosClient->getObject('abcdef-ghijkl-mnopqr-12345', $from . '- . $to)->__toString();
}

```

### Handling errors

This library converts any errors that the WOS Server emits into `WosException` exceptions.  The
exception will contain a code that corresponds to the official DDN API specification, as well as a
user-friendly message:

```php

use WosClient\WosException;

try {
   $wosClient->getObject('does-not-exist');
} catch (WosException $e) {

    // WOS User-friendly message, e.g., 'Object cannot be located'
    echo $e->getMessage();
    
    // WOS Code, e.g., 207
    echo $e->getCode();
    
    // WOS Error Name (machine-friendly, uses CamelCase), e.g., ObjNotFound
    echo $e->getErrorName();
}

```

Note that `WosException` is NOT thrown in the case of any HTTP errors.  `WosException` is only
thrown in the case that the client successfully connects to the server and receives an HTTP response,
but WOS couldn't process the request.

You can catch HTTP errors separately, such as 400, 500, connection timeout, etc.  To do this, use GuzzleExceptions:

```php

use WosClient\WosException;
use GuzzleHttp\Exception\GuzzleException;

try {
   $wosClient->getObject('does-not-exist');
}
catch (WosException $e) {

    // WOS User-friendly message, e.g., 'Object cannot be located'
    echo 'WOS Error: ' . $e->getMessage();
    
}
catch (GuzzleException $e) {

   // HTTP Exception thrown by Guzzle when attempting to connect and get the response
   echo 'HTTP Error: ' . $e->getMessage();
}

```

The Guzzle library contains a number of different exception classes for specific
categories of errors, in case you wish to be more specific about your exception handling.

### Instantiating with a custom Guzzle 6 client instance

You may wish to use your own Guzzle 6 Client instance to make requests to the WOS server.
For example, if you have setup your own middleware, or wish to use custom HTTP defaults (such as
`connect_timeout`).

To do this, simply use the main constructor for the `WosClient` class instead of the `build()`
constructor. 

The `base_uri` parameter **MUST** be set when you setup the Guzzle client.  You also may wish to
set the `x-ddn-policy` header, so that you do not need to set that on the options for each request:

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


$wosClient = new WosClient($guzzleClient);

```

### Using a library besides Guzzle 6

For various reasons, you may not be able to use the recommended Guzzle 6 implementation of this library.
For example, you may wish to use [zend-diactoros](https://github.com/zendframework/zend-diactoros) or an
earlier version of Guzzle.

In order to do this, you must convert any HTTP Responses that these libraries return into PSR-7-compliant
response objects.  Then, you can create your own implementation of the `WosClient\WosClientInterface` interface.
That file contains comprehensive documentation for how each method should behave.

Note that if you do create a custom implementation of `WosClientInterface`, you should throw `WosException` ONLY
in the case that the `x-ddn-status` response header contains a non 0 status.  HTTP transport exceptions are out
of the scope of this library, and should be handled independently of these classes.  See *Handling Errors* above
for tips on how the default implementation handles this case.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

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
