<?php

namespace WosClient\UnitTest;

use PHPUnit_Framework_TestCase;
use WosClient\WosObjectId;

/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 3:26 PM
 */
class WosObjectIdTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensure object instantiates successfully
     */
    public function testInstantiateSucceedsWithValidResponse()
    {
        $obj = new WosObjectId($this->getMockRequest());
        $this->assertInstanceOf(WosObjectId::class, $obj);
    }

    /**
     * Ensure object does not instantiate succesfully if missing 'x-ddn-oid' header
     *
     * @expectedException \WosClient\MissingRequiredHeaderException
     */
    public function testInstantiateFailsWhenMissingOidHeader()
    {
        new WosObjectId($this->getMockRequest(false));
    }

    /**
     * Test that get() methods return expected values
     */
    public function testGettersReturnExpectedValues()
    {
        $obj = new WosObjectId($this->getMockRequest());
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
