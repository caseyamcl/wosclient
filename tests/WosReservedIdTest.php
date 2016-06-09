<?php

namespace WosClient;

use PHPUnit_Framework_TestCase;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 3:26 PM
 */
class WosReservedIdTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensure object instantiates successfully
     */
    public function testInstantiateSucceedsWithValidResponse()
    {
        $obj = new WosReservedId($this->getMockRequest());
        $this->assertInstanceOf('WosClient\WosReservedId', $obj);
    }

    /**
     * Ensure object does not instantiate succesfully if missing 'x-ddn-oid' header
     *
     * @expectedException \WosClient\MissingRequiredHeaderException
     */
    public function testInstantiateFailsWhenMissingOidHeader()
    {
        new WosReservedId($this->getMockRequest(false));
    }

    /**
     * Test that get() methods return expected values
     */
    public function testGettersReturnExpectedValues()
    {
        $obj = new WosReservedId($this->getMockRequest());
        $this->assertEquals('abc-123', $obj->getObjectId());
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $obj->getHttpResponse());
    }

    protected function getMockRequest($withHeader = true)
    {
        $mockRequest = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $mockRequest->shouldReceive('hasHeader')->andReturn($withHeader);
        $mockRequest->shouldReceive('getHeaderLine')->andReturn($withHeader ? 'abc-123' : '');
        return $mockRequest;
    }

}
