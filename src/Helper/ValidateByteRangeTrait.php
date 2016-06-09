<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 9:46 AM
 */

namespace WosClient\Helper;

use WosClient\Exception\InvalidHeaderValueException;

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
     * @param string $byteRange
     * @throws \RuntimeException  If invalid byte range specified
     */
    protected function validateByteRange($byteRange)
    {
        if (! preg_match('/^(\d+)?-(\d+)?$/', $byteRange)) {
            throw new InvalidHeaderValueException(
                "Invalid range specification (must be in ###-### format); multiple rangers are not supported"
            );
        }
    }
}
