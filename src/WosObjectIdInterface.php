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
