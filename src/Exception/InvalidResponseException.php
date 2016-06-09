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
 * Missing Header Exception
 *
 * This exception is thrown when a WosClient library expects a header
 * to be present in the HTTP response, but it does not exist
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class InvalidResponseException extends WosException
{
    /**
     * @var string
     */
    private $parameterName;

    /**
     * InvalidResponseException constructor.
     *
     * @param string $oarameterName The parameter/HTTP header that was expected in the response (e.g. 'x-ddn-oid')
     * @param string $requestAction The action that was requested, if known (e.g., 'getMetdata()')
     */
    public function __construct($oarameterName, $requestAction = '')
    {
        $this->parameterName = $oarameterName;

        $message = sprintf(
            "Response did not contain '%s' header. Are you sure that you made a %s to a WOS Server?",
            $oarameterName,
            $requestAction ? $requestAction . ' request' : 'request'
        );

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getParameterName()
    {
        return $this->parameterName;
    }
}
