<?php

/**
 * PHP Client for DDN Web Object Scalar (WOS) API
 *
 * @package Wosclient
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    https://github.com/caseyamcl/wosclient
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace WosClient;

use GuzzleHttp\Psr7\Stream;
use WosClient\Exception\WosServerException;

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
     * Common other Guzzle HTTP options you can specify:
     *
     * [
     *    'headers' => [
     *      'x-ddn-put-on-close'     => true,
     *      'x-ddn-put-idle-timeout' => (int)
     *    ]
     * ]
     *
     * * 'x-ddn-put-on-close' (bool)
     *   This is FALSE by default; set it to TRUE if you wish for the WOS to
     *   keep the partial written data even if the request fails before it is
     *   finished
     *
     * * 'x-ddn-put-idle-timeout' (int)
     *   Include this header with a value between 5 and 3600 seconds to specify
     *   that the connection should automatically be terminated if the server
     *   has not received any data in the specified number of seconds
     *
     * @param  string|resource|Stream      $data     Specify raw data, a fopen() resource, or a PSR-7 stream
     * @param  array                       $meta     Optionally specify key/value pairs for object metadata
     * @param  string|WosObjectIdInterface $objectId If EMPTY, Object ID (OID) will automatically be assigned
     * @param  array                       $options  Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return WosObjectIdInterface
     * @throws WosServerException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function putObject($data, array $meta = [], $objectId = '', array $options = []);

    /**
     * Get a WOS object
     *
     * This gets the WOS object as an entity where the raw data can be
     * streamed.
     *
     * If you wish to download the object immediately upon calling this method,
     * add ['stream' => false] to the request options
     *
     * Common other Guzzle HTTP options you can specify:
     *
     * [
     *    'headers' => [
     *      'x-ddn-no-meta'         => true,
     *      'x-ddn-buffered'        => false,
     *      'x-ddn-integrity-check' => false
     *    ]
     * ]
     *
     * * 'x-ddn-no-meta' (bool)
     *    This is FALSE by default; metadata is returned with objects.
     *    Set this to TRUE to omit metdata from response (will be an empty array)
     *
     * * 'x-ddn-buffered' (bool)
     *   This is TRUE by default, but you should set it to false when
     *   using '$byteRange' on large objects
     *
     * * 'x-ddn-integrity-check' (bool)
     *   This is TRUE by default, but you should set it to false when using
     *   '$byteRange' on large objects
     *
     * Refer to the DDN WOS API documentation for more details of headers
     *
     * @param  string|WosObjectIdInterface $objectId  The WOS ObjectID (OID)
     * @param  string                      $byteRange Specify byte range (as string "###-###") for
     *                                               partial responses
     * @param  array                       $options   Additional request options (see
     *                                               http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return WosObjectInterface
     * @throws WosServerException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function getObject($objectId, $byteRange = '', array $options = []);

    /**
     * Get metadata for an object
     *
     * @param  string|WosObjectIdInterface $objectId The WOS ObjectID (OID)
     * @param  array                       $options  Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     *                                               (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return WosObjectMetadataInterface
     * @throws WosServerException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function getMetadata($objectId, array $options = []);

    /**
     * Delete an object
     *
     * @param  string|WosObjectIdInterface $objectId The WOS ObjectID (OID)
     * @param  array                       $options  Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     *                                               (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @throws WosServerException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function deleteObject($objectId, array $options = []);

    /**
     * Reserve an object ID
     *
     * @param  array $options Additional request options (see http://docs.guzzlephp.org/en/latest/request-options.html)
     * @return string         The Object ID (OID)
     * @return WosObjectId
     * @throws WosServerException   If WOS server generates an error (x-ddn-status != 0), an exception is thrown
     */
    public function reserveObject(array $options = []);
}
