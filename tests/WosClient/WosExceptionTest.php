<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 3:48 PM
 */

namespace WosClient;

use SebastianBergmann\CodeCoverage\RuntimeException;

/**
 * Class WosExceptionTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceedsWithValidErrorCode()
    {
        $obj = new WosException(200);
        $this->assertInstanceOf('\WosClient\WosException', $obj);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInstantiateThrowsExceptionWithZeroErrorCode()
    {
        new WosException(0);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInstantiateThrowsExceptionWithNonExistentErrorCode()
    {
        $num = rand(223, 1000); // these values do not exist
        new WosException($num);
    }

    /**
     * @param $code
     * @param $expected
     * @dataProvider errorNameDataProvider
     */
    public function testGetErrorNameReturnsExpectedNameForGivenCode($code, $expected)
    {
        $obj = new WosException($code);
        $this->assertEquals($expected, $obj->getErrorName());
    }

    /**
     * @param $code
     * @param $expected
     * @dataProvider errorMeaningDataProvider
     */
    public function testGetErrorMeaningReturnsExpectedNameForGivenCode($code, $expected)
    {
        $obj = new WosException($code);
        $this->assertEquals($expected, $obj->getErrorMeaning());
    }

    public function testMessageIsCustomForGivenMessage()
    {
        $obj = new WosException(220, 'Custom Message');
        $this->assertEquals('Custom Message', $obj->getMessage());
    }

    /**
     * @param $code
     * @param $expected
     * @dataProvider errorMeaningDataProvider
     */
    public function testMessageIsEqualToMeaningByDefault($code, $expected)
    {
        $obj = new WosException($code);
        $this->assertEquals($expected, $obj->getMessage());
    }

    /**
     * Data provider to get error codes and meanings
     *
     * @return array|array[]
     */
    public function errorMeaningDataProvider()
    {
        $refl = new \ReflectionClass('\WosClient\WosException');
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
        $refl = new \ReflectionClass('\WosClient\WosException');
        $vals = $refl->getStaticProperties()['codeNames'];

        $out = array();
        foreach ($vals as $code => $val) {
            $out[] = [$code, $val];
        }
        return $out;
    }


}
