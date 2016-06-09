<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 9:46 AM
 */

namespace WosClient;

/**
 * Class ValidateByteRangeTrait
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
            throw new \RuntimeException(
                "Invalid range specification (must be in ###-### format); multiple rangers are not supported"
            );
        }
    }
}
