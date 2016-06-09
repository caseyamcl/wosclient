<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 4:38 PM
 */

namespace WosClient\UnitTest;


use GuzzleHttp\Client;
use WosClient\WosClient;

class WosClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the build constructor works correctly with valid values
     */
    public function testBuildReturnsValidInstance()
    {
        $obj = WosClient::build('https://wos.example.org', 'policyid', ['connect_timeout' => 100]);

        $this->assertInstanceOf('WosClient\WosClient', $obj);
        $this->assertEquals(100,                       $obj->getHttpClient()->getConfig('connect_timeout'));
        $this->assertEquals('https://wos.example.org', $obj->getHttpClient()->getConfig('base_uri'));
        $this->assertArrayHasKey('x-ddn-policy', $obj->getHttpClient()->getConfig('headers'));
    }

    /**
     * Test that the normal constructor works when the Guzzle
     * client passed in contains 'base_uri' config value
     */
    public function testConstructCreatesInstanceWithValidGuzzleClient()
    {
        $client = new Client(['base_uri' => 'https://wos.example.org']);
        $obj = new WosClient($client);
        $this->assertInstanceOf('WosClient\WosClient', $obj);
    }

    /**
     * Test that the normal constructor throws an exception when the Guzzle
     * client passed in does not contain the required 'base_uri' config value
     *
     * @expectedException \RuntimeException
     */
    public function testConstructThrowsExceptionWhenMissingBaseUriConfigValue()
    {
        $client = new Client();
        new WosClient($client);
    }
}
