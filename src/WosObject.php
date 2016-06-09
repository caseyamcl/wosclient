<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 1:18 PM
 */

namespace WosClient;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

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
            throw new MissingRequiredHeaderException('x-ddn-oid', 'get object');
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
