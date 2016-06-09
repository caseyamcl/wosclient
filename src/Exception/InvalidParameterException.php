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

namespace WosClient\Exception;

/**
 * Invalid Header Value Exception
 *
 * This exception is thrown when a request to the WOS server contains
 * an invalid value.
 *
 * Throw this when validating parameters on the client-side,
 * before the request is sent to the server.
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class InvalidParameterException extends WosException
{
    // pass..
}
