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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WosClient\Exception\InvalidResponseException;

/**
 * WosObject
 *
 * Represents object returned by a WOS getObject request
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosObject implements WosObjectInterface
{
    /**
     * @var StreamInterface
     */
    private $responseData;

    /**
     * @var WosObjectMetadata
     */
    private $metadata;

    /**
     * @var WosObjectId
     */
    private $objectId;

    /**
     * WosObject constructor.
     *
     * @param ResponseInterface $httpResponse
     */
    public function __construct(ResponseInterface $httpResponse)
    {
        if (! $httpResponse->hasHeader('x-ddn-oid')) {
            throw new InvalidResponseException('x-ddn-oid', 'get object');
        }

        $this->responseData = $httpResponse->getBody();
        $this->metadata     = new WosObjectMetadata($httpResponse);
        $this->objectId     = new WosObjectId($httpResponse);
    }

    /**
     * @return StreamInterface
     */
    public function getData()
    {
        return $this->responseData;
    }

    /**
     * @return WosObjectMetadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return WosObjectId
     */
    public function getId()
    {
        return $this->objectId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->responseData->__toString();
    }
}
