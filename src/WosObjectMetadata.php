<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 1:30 PM
 */

namespace WosClient;

use Countable;
use Psr\Http\Message\ResponseInterface;
use Traversable;
use WosClient\Helper\ArrayToMetadataStringTrait;

/**
 * Class WosObjectMetadata
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosObjectMetadata implements \IteratorAggregate, WosObjectMetadataInterface
{
    use ArrayToMetadataStringTrait;

    /**
     * Unknown value
     */
    const UNKNOWN = null;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var int
     */
    private $objectSize;

    /**
     * WosObjectMetadata constructor.
     *
     * @param ResponseInterface $httpResponse
     */
    public function __construct(ResponseInterface $httpResponse)
    {
        $this->metadata   = json_decode('{' . $httpResponse->getHeaderLine('x-ddn-meta') . '}', true);
        $this->objectSize = $httpResponse->hasHeader('x-ddn-length')
            ? (int) $httpResponse->getHeaderLine('x-ddn-length')
            : self::UNKNOWN;
    }

    /**
     * Return representation of the metadata as it would appear as a x-ddn-metadata value
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->metadataToString($this->toArray());
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @return array|mixed[]
     */
    public function toArray()
    {
        return $this->metadata;
    }

    /**
     * Returns data length in BYTES if known, NULL otherwise
     *
     * @return int|null
     */
    public function getObjectSize()
    {
        return $this->objectSize;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     * @return boolean true on success or false on failure.
     *                      </p>
     *                      <p>
     *                      The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->metadata);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException("Cannot set $offset for WosObjectMetadata.  Metadata is immutable");
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException("Cannot unset $offset for WosObjectMetadata.  Metadata is immutable");
    }

    /**
     * Retrieve an external iterator
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *        <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->metadata);
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->metadata);
    }
}
