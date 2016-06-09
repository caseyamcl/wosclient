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

use Psr\Http\Message\StreamInterface;

/**
 * WosObject
 *
 * Represents object returned by a WOS getObject request
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
interface WosObjectInterface
{
    /**
     * Get the data
     *
     * @return StreamInterface
     */
    public function getData();

    /**
     * Get the metadata
     *
     * @return WosObjectMetadataInterface
     */
    public function getMetadata();

    /**
     * Return string representation of the object data
     *
     * WARNING: If object is very large, this MAY cause PHP to run over its
     * memory limit and crash.
     *
     * You can check how large an object is by calling
     * $wosObject->getMetadata()->getObjectSize() to get the number of bytes
     * before attempting to convert the object to a string.
     *
     * If the object is very large, then you should use the features of the
     * StreamInterface to stream the data rather than load it into memory all
     * at once.
     *
     * @return string
     */
    public function __toString();

    /**
     * Get the Object ID
     *
     * @return WosObjectId
     */
    public function getId();
}
