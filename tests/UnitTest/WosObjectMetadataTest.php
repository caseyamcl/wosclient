<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 4:06 PM
 */

namespace WosClient\UnitTest;

use WosClient\WosObjectMetadata;

class WosObjectMetadataTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_METADATA = '"foo": "bar", "baz": "biz"';

    public function testInstantiateSucceedsWithValidResponse()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $this->assertInstanceOf('WosClient\WosObjectMetadata', $obj);

    }

    public function testInstantiateCreatesEmptyMetadataForNoMetadataHeader()
    {
        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeaderLine')->with('x-ddn-meta')->andReturn('');
        $response->shouldReceive('getHeaderLine')->with('x-ddn-length')->andReturn('');
        $response->shouldReceive('hasHeader')->with('x-ddn-length')->andReturn(false);

        $obj = new WosObjectMetadata($response);
        $this->assertEquals(0, $obj->count());
        $this->assertEquals([], $obj->toArray());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testOffsetSetThrowsException()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $obj['foo'] = 'bar';
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testOffsetUnsetThrowsException()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        unset($obj['foo']);
    }

    public function testToArrayReturnsExpected()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $this->assertEquals(['foo' => 'bar', 'baz' => 'biz'], $obj->toArray());
    }

    public function testGetLengthReturnsNullForUnknownLength()
    {
        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeaderLine')->with('x-ddn-meta')->andReturn('');
        $response->shouldReceive('getHeaderLine')->with('x-ddn-length')->andReturn('');
        $response->shouldReceive('hasHeader')->with('x-ddn-length')->andReturn(false);

        $obj = new WosObjectMetadata($response);
        $this->assertNull($obj->getObjectSize());
    }

    public function testGetLengthReturnsIntegerValueForKnownLength()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $this->assertInternalType('integer', $obj->getObjectSize());
    }

    public function testArrayAccessMethods()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $this->assertEquals('bar', $obj['foo']);
        $this->assertEquals('biz', $obj['baz']);
        $this->assertTrue(isset($obj['foo']));
        $this->assertFalse(isset($obj['nope']));
    }

    public function testGetIteratorReturnsValidIterator()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $this->assertInstanceOf('\Iterator', $obj->getIterator());
    }

    public function testCountReturnsCorrectValue()
    {
        $obj = new WosObjectMetadata($this->getDefaultTestResponse());
        $this->assertEquals(2, count($obj));
    }

    protected function getDefaultTestResponse()
    {
        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getHeaderLine')->with('x-ddn-meta')->andReturn(static::EXAMPLE_METADATA);
        $response->shouldReceive('getHeaderLine')->with('x-ddn-length')->andReturn(26);
        $response->shouldReceive('hasHeader')->with('x-ddn-length')->andReturn(true);
        return $response;
    }

}
