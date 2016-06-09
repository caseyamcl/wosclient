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
use WosClient\Exception\InvalidResponseException;

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
            throw new InvalidResponseException('x-ddn-oid', 'reserve object');
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
