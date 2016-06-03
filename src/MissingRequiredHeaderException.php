<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 3:17 PM
 */

namespace WosClient;

/**
 * Class MissingRequiredHeaderException
 *
 * This exception is thrown when a WosClient library expects a header
 * to be present in the HTTP response, but it does not exist
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MissingRequiredHeaderException extends \RuntimeException
{
    /**
     * @var string
     */
    private $headerName;

    /**
     * MissingRequiredHeaderException constructor.
     *
     * @param string $headerName     The header that was expected (e.g. 'x-ddn-oid')
     * @param string $requestAction  The action name (e.g., 'getMetdata()') to include in the message, if known
     */
    public function __construct($headerName, $requestAction = '')
    {
        $this->headerName = $headerName;

        $message = sprintf(
            "Response did not contain '%s' header. Are you sure that you made a %s to a WOS Server?",
            $headerName,
            $requestAction ? $requestAction . ' request' : 'request'
        );

        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getHeaderName()
    {
        return $this->headerName;
    }
}
