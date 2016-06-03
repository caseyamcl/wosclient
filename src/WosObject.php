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
class WosObject
{
    /**
     * @var StreamInterface
     */
    private $responseData;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var string
     */
    private $objectId;

    /**
     * @var ResponseInterface
     */
    private $httpResponse;

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

        $this->httpResponse = $httpResponse;
        $this->responseData = $httpResponse->getBody();
        $this->metadata     = new WosObjectMetadata($httpResponse);
        $this->objectId     = $httpResponse->getHeaderLine('x-ddn-oid');
    }

    /**
     * @return StreamInterface
     */
    public function getData()
    {
        return $this->responseData;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->objectId;
    }

    /**
     * @return ResponseInterface
     */
    public function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->responseData->__toString();
    }
}
