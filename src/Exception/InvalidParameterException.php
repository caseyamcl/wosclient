<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 1:26 PM
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
