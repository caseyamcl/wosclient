<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 1:56 PM
 */

namespace WosClient;

use Psr\Http\Message\ResponseInterface;

/**
 * Class WosObjectId
 *
 * Simple value object to represent a response for a WOS reserve OID request
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosObjectId implements WosObjectIdInterface
{
    /**
     * @var string
     */
    private $objectId;

    /**
     * WosObjectId constructor.
     *
     * @param ResponseInterface $httpResponse
     */
    public function __construct(ResponseInterface $httpResponse)
    {
        if (! $httpResponse->hasHeader('x-ddn-oid')) {
            throw new MissingRequiredHeaderException('x-ddn-oid', 'reserve object');
        }

        $this->objectId = $httpResponse->getHeaderLine('x-ddn-oid');
    }

    /**
     * @return string
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getObjectId();
    }
}
