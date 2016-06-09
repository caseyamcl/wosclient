<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 3:45 PM
 */

namespace WosClient\UnitTest;

use WosClient\MissingRequiredHeaderException;

/**
 * Class MissingRequiredHeaderExceptionTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class MissingRequiredHeaderExceptionTest extends \PHPUnit_Framework_TestCase
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

        $obj = new MissingRequiredHeaderException($headerName, $requestAction);
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
