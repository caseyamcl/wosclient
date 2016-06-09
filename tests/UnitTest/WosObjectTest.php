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

use WosClient\WosObject;

class WosObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateSucceedsWithValidResponse()
    {
        $obj = new WosObject($this->getMockValidResponse());
        $this->assertInstanceOf('WosClient\WosObject', $obj);
    }

    /**
     * @expectedException \WosClient\Exception\InvalidResponseException
     */
    public function testInstantiateThrowsExceptionIfMissingOidHeader()
    {
        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('hasHeader')->with('x-ddn-oid')->andReturn(false);

        new WosObject($response);
    }

    public function testGetMetadataReturnsMetadataObjectAlways()
    {
        $objWithMetadata    = new WosObject($this->getMockValidResponse());
        $objWithoutMetadata = new WosObject($this->getMockValidResponse(false));

        $this->assertEquals(2, count($objWithMetadata->getMetadata()));
        $this->assertInstanceOf('\WosClient\WosObjectMetadata', $objWithMetadata->getMetadata());

        $this->assertEquals(0, count($objWithoutMetadata->getMetadata()));
        $this->assertInstanceOf('\WosClient\WosObjectMetadata', $objWithoutMetadata->getMetadata());
    }

    public function testGetIdReturnsCorrectId()
    {
        $obj = new WosObject($this->getMockValidResponse());
        $this->assertEquals('abc-123', $obj->getId());
    }

    public function testGetDataReturnsStreamInterface()
    {
        $obj = new WosObject($this->getMockValidResponse());
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $obj->getData());

    }

    public function testToStringReturnsCompleteStreamData()
    {
        $obj = new WosObject($this->getMockValidResponse());
        $this->assertEquals('abc123', $obj->__toString());
    }

    protected function getMockValidResponse($includeMetadata = true)
    {
        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface');

        // Metadata stuff
        if ($includeMetadata) {
            $response->shouldReceive('getHeaderLine')->with('x-ddn-meta')->andReturn(WosObjectMetadataTest::EXAMPLE_METADATA);
            $response->shouldReceive('getHeaderLine')->with('x-ddn-length')->andReturn(26);
            $response->shouldReceive('hasHeader')->with('x-ddn-length')->andReturn(true);
        }
        else {
            $response->shouldReceive('getHeaderLine')->with('x-ddn-meta')->andReturn('');
            $response->shouldReceive('getHeaderLine')->with('x-ddn-length')->andReturn(0);
            $response->shouldReceive('hasHeader')->with('x-ddn-length')->andReturn(false);
        }

        // WosObject stuff
        $response->shouldReceive('hasHeader')->with('x-ddn-oid')->andReturn(true);
        $response->shouldReceive('getHeaderLine')->with('x-ddn-oid')->andReturn('abc-123');
        $response->shouldReceive('getBody')->andReturn($this->getMockStream());

        return $response;
    }

    protected function getMockStream()
    {
        $stream = \Mockery::mock('Psr\Http\Message\StreamInterface');
        $stream->shouldReceive('__toString')->andReturn('abc123');

        return $stream;
    }
}
