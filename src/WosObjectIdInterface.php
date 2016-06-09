<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 12:52 PM
 */
namespace WosClient;


/**
 * Class WosObjectId
 *
 * Simple value object to represent a response for a WOS reserve OID request
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
interface WosObjectIdInterface
{
    /**
     * Get the object ID
     *
     * @return string
     */
    public function getObjectId();

    /**
     * Convert object ID to string
     * 
     * @return string
     */
    public function __toString();
}
