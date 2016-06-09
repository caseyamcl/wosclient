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

namespace WosClient\UnitTest;

use WosClient\Exception\InvalidResponseException;

/**
 * Class InvalidResponseExceptionTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class InvalidResponseExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $headerName
     * @param string $requestAction
     * @dataProvider testCases
     */
    public function testMessageEqualsExpected($headerName, $requestAction = '')
    {
        $expected = sprintf(
            "Response did not contain '%s' header. Are you sure that you made a %s to a WOS Server?",
            $headerName,
            $requestAction ? $requestAction . ' request' : 'request'
        );

        $obj = new InvalidResponseException($headerName, $requestAction);
        $this->assertEquals($expected, $obj->getMessage());
    }

    public function testCases()
    {
        return [
            ['foo-header', ''],
            ['bar-header', 'do bar']
        ];
    }
}
