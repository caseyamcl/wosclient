<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 4:38 PM
 */

namespace WosClient;


use GuzzleHttp\Client;

class WosClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testBuildReturnsValidInstance()
    {
        $obj = WosClient::build('https://wos.example.org', 'policyid', ['connect_timeout' => 100]);

        $this->assertInstanceOf('WosClient\WosClient', $obj);
        $this->assertEquals(100,                       $obj->getHttpClient()->getConfig('connect_timeout'));
        $this->assertEquals('https://wos.example.org', $obj->getHttpClient()->getConfig('base_uri'));
        $this->assertArrayHasKey('x-ddn-policy', $obj->getHttpClient()->getConfig('headers'));
    }

    public function testConstructCreatesInstanceWithValidGuzzleClient()
    {
        $client = new Client(['base_uri' => 'https://wos.example.org']);
        $obj = new WosClient($client);
        $this->assertInstanceOf('WosClient\WosClient', $obj);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructThrowsExceptionWhenMissingBaseUriConfigValue()
    {
        $client = new Client();
        new WosClient($client);
    }

    
}
