<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 12:50 PM
 */
namespace WosClient;


/**
 * Class WosObjectMetadata
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
interface WosObjectMetadataInterface extends \ArrayAccess, \Traversable, \Countable
{
    /**
     * Get metadata value by its key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * Check if a metadata value exists for a given key
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Dump metadata key/values as array
     *
     * @return array|mixed[]
     */
    public function toArray();

    /**
     * Returns data length in BYTES of the object data if known, NULL otherwise
     *
     * @return int|null
     */
    public function getObjectSize();

    /**
     * Return representation of the metdata as it would appear as a x-ddn-metadata value
     * 
     * @return mixed
     */
    public function __toString();
}
