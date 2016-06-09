<?php

namespace WosClient;

use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

/**
 * WOS Client Interface
 *
 * This is a Guzzle 6.x WOS Client implementation
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
interface WosClientInterface
{
    /**
     * Put an object
     *
     * If $objectId is specified, then this method will PUT the object in the
     * storage system with the specified ID.
     *
     * If $objectId is NOT specified, then this method wilL POST the object to
     * the server, and the server will return the ID
     *
     * Common other options you can specify:
     *
     * [
     *    'headers' => [
     *      'x-ddn-put-on-close'     => true,   // This is FALSE by default; if WOS PUT fails before the request finishes, then put the data anyway
     *      'x-ddn-put-idle-timeout' => (int)  // This is TRUE by default, but you should set it to false when using '$byteRange' on large objects
     *    ]
     * ]
     *
     * @param string|resource|Stream $data     Specify raw data, a fopen() resource, or a PSR-7 stream
     * @param array                  $meta     Optionally specify key/value pairs for object metadata
     * @param string|WosObjectId     $objectId If EMPTY, Object ID (OID) will automatically be assigned
     * @param array                  $options  Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return WosObjectId
     * @throws WosException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function putObject($data, array $meta = [], $objectId = '', array $options = []);

    /**
     * Get a WOS object
     *
     * This gets the WOS object as an entity where the raw data can be streamed.
     *
     * If you wish to download the object immediately upon calling this method,
     * add ['stream' => false] to the request options
     *
     * Common other options you can specify:
     *
     * [
     *    'headers' => [
     *      'x-ddn-no-meta'         => true,   // By default, metadata is returned with objects.  Specify this to not return metadata (will be an empty array)
     *      'x-ddn-buffered'        => false,  // This is TRUE by default, but you should set it to false when using '$byteRange' on large objects
     *      'x-ddn-integrity-check' => false   // This is TRUE by default, but you should set it to false when using '$byteRange' on large objects
     *    ]
     * ]
     *
     * Refer to the DDN WOS API documentation for more details of headers
     *
     * @param string $objectId   The WOS ObjectID (OID)
     * @param string $byteRange  Specify byte range (as string "###-###") for partial responses
     * @param array  $options    Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return WosObject
     * @throws WosException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function getObject($objectId, $byteRange = '', array $options = []);

    /**
     * Get metadata for an object
     *
     * @param string $objectId   The WOS ObjectID (OID)
     * @param array  $options    Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return WosObjectMetadata
     * @throws WosException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function getMetadata($objectId, array $options = []);

    /**
     * Delete an object
     *
     * @param string $objectId   The WOS ObjectID (OID)
     * @param array $options
     * @return ResponseInterface
     * @throws WosException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function deleteObject($objectId, array $options = []);

    /**
     * Reserve an object ID
     *
     * @param array $options    Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return string  The Object ID (OID)
     * @return WosObjectId
     * @throws WosException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function reserveObject(array $options = []);
}
