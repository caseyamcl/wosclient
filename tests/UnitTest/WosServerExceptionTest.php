<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 3:48 PM
 */

namespace WosClient\UnitTest;

use WosClient\Exception\WosServerException;

/**
 * Class WosServerExceptionTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosServerExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceedsWithValidErrorCode()
    {
        $obj = new WosServerException(200);
        $this->assertInstanceOf(WosServerException::class, $obj);
    }

    /**
     * @expectedException \LogicException
     */
    public function testInstantiateThrowsExceptionWithZeroErrorCode()
    {
        new WosServerException(0);
    }

    /**
     * Test that unknown codes work, but throw the 'UNKNOWN' message.
     */
    public function testInstantiateThrowsExceptionWithNonExistentErrorCode()
    {
        $num = rand(223, 1000); // these values do not exist
        $exception = new WosServerException($num);
        $this->assertEquals(WosServerException::UNKNOWN_NAME, $exception->getErrorName());
    }

    /**
     * @param $code
     * @param $expected
     * @dataProvider errorNameDataProvider
     */
    public function testGetErrorNameReturnsExpectedNameForGivenCode($code, $expected)
    {
        $obj = new WosServerException($code);
        $this->assertEquals($expected, $obj->getErrorName());
    }

    /**
     * @param $code
     * @param $expected
     * @dataProvider errorMeaningDataProvider
     */
    public function testGetErrorMeaningReturnsExpectedNameForGivenCode($code, $expected)
    {
        $obj = new WosServerException($code);
        $this->assertEquals($expected, $obj->getErrorMeaning());
    }

    public function testMessageIsCustomForGivenMessage()
    {
        $obj = new WosServerException(220, 'Custom Message');
        $this->assertEquals('Custom Message', $obj->getMessage());
    }

    /**
     * @param $code
     * @param $expected
     * @dataProvider errorMeaningDataProvider
     */
    public function testMessageIsEqualToMeaningByDefault($code, $expected)
    {
        $obj = new WosServerException($code);
        $this->assertEquals($expected, $obj->getMessage());
    }

    /**
     * Data provider to get error codes and meanings
     *
     * @return array|array[]
     */
    public function errorMeaningDataProvider()
    {
        $refl = new \ReflectionClass(WosServerException::class);
        $vals = $refl->getStaticProperties()['codeMeanings'];


        $out = array();
        foreach ($vals as $code => $val) {
            $out[] = [$code, $val];
        }
        return $out;
    }

    /**
     * Data provider to get error codes and names
     *
     * @return array|array[]
     */
    public function errorNameDataProvider()
    {
        $refl = new \ReflectionClass(WosServerException::class);
        $vals = $refl->getStaticProperties()['codeNames'];

        $out = array();
        foreach ($vals as $code => $val) {
            $out[] = [$code, $val];
        }
        return $out;
    }


}
