<?php

/**
 * PHP Client for DDN Web Object Scalar (WOS) API
 *
 * @license http://opensource.org/licenses/MIT
 * @link    https://github.com/caseyamcl/wosclient
 * @version 1.0
 * @package caseyamcl/wosclient
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

namespace WosClient\Helper;

/**
 * Helper to turn an array of key/value pairs into a string
 *
 * The resulting string is compatible with the x-ddn-metadata header
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait ArrayToMetadataStringTrait
{
    /**
     * Prepare metadata for DDN header
     *
     * Converts array into JSON, and removes surrounding brackets
     * Returns an empty string for an empty array
     *
     * @param  array $meta
     * @return string
     */
    protected function metadataToString(array $meta = [])
    {
        return preg_replace('/^\{(.+?)?\}$/', '$1', json_encode($meta, JSON_FORCE_OBJECT));
    }
}
