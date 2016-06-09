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

use WosClient\Exception\InvalidParameterException;

/**
 * Helper to validate a byte range header value
 *
 * The value must be in ###-### format
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
trait ValidateByteRangeTrait
{
    /**
     * Verifies that byte range is in ####-#### format
     *
     * @param  string $byteRange
     * @throws \RuntimeException  If invalid byte range specified
     */
    protected function validateByteRange($byteRange)
    {
        if (! preg_match('/^(\d+)?-(\d+)?$/', $byteRange)) {
            throw new InvalidParameterException(
                "Invalid range specification (must be in ###-### format); multiple rangers are not supported"
            );
        }
    }
}
