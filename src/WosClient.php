<?php

namespace WosClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;

/**
 * WOS Client Implementation for current/latest WOS API
 *
 * Tested against WOS Version 2.1.1, but should work in newer versions, so
 * long as DDN doesn't mess with their API much.
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosClient implements WosClientInterface
{
    use Helper\ValidateByteRangeTrait;
    use Helper\ArrayToMetadataStringTrait;

    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * Build a WOS Client from parameters
     *
     * @param string $wosUrl
     * @param string $wosPolicy
     * @param array  $guzzleOptions
     * @return WosClient
     */
    public static function build($wosUrl, $wosPolicy = '', array $guzzleOptions = [])
    {
        $guzzleParams = array_merge_recursive([
            'base_uri' => $wosUrl,
            'headers'  => [
                'x-ddn-policy' => $wosPolicy
            ]
        ], $guzzleOptions);


        return new static(new Client($guzzleParams));
    }

    /**
     * WOS Client constructor
     *
     * The constructor expects a guzzleClient with the base_url value set, and
     * any default options that should be used on all requests.
     *
     * For convenience, you probably want to set the 'x-ddn-policy' header by
     * default.  See the self::build() method above for an example of setting
     * this.
     *
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        if (! isset($guzzleClient->getConfig()['base_uri'])) {
            throw new \RuntimeException("Cannot instantiate a WosClient without 'base_uri' set in Guzzle");
        }

        $this->guzzleClient = $guzzleClient;
    }

    /**
     * {@inheritdoc}
     */
    public function putObject($data, array $meta = [], $objectId = '', array $options = [])
    {
        $options = array_merge_recursive([
            'body'       => $data,
            'headers'    => array_filter([
                'x-ddn-meta' => $this->metadataToString($meta),
                'x-ddn-oid'  => (string) $objectId
            ])
        ], $options);


        $resp = $this->sendRequest(
            'post',
            $objectId ? '/cmd/putoid' : '/cmd/put',
            $options
        );

        $this->checkResponse($resp);
        return new WosObjectId($resp);
    }

    /**
     * {@inheritdoc}
     */
    public function getObject($objectId, $byteRange = '', array $options = [])
    {
        // Add range to options if specified
        $options = array_merge_recursive([
            'headers' => array_filter([
                'range' => $byteRange ? ('bytes=' . $this->validateByteRange($byteRange)) : ''
            ])
        ], $options);

        $resp = $this->sendRequest('get', '/objects/' . (string) $objectId, $options);

        $this->checkResponse($resp);
        return new WosObject($resp);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($objectId, array $options = [])
    {
        $resp = $this->sendRequest('head', '/objects/' . $objectId, $options);
        $this->checkResponse($resp);
        return new WosObjectMetadata($resp);

    }

    /**
     * {@inheritdoc}
     */
    public function deleteObject($objectId, array $options = [])
    {
        // OID is sent in header, not in URI
        $options = array_merge_recursive([
            'headers' => [
                'x-ddn-oid' => (string) $objectId
            ]
        ], $options);

        $resp = $this->sendRequest('post', '/cmd/delete', $options);
        $this->checkResponse($resp);
    }

    /**
     * {@inheritdoc}
     */
    public function reserveObject(array $options = [])
    {
        $resp = $this->sendRequest('post', '/cmd/reserve', $options);

        $this->checkResponse($resp);
        return new WosObjectId($resp);
    }

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->guzzleClient;
    }

    /**
     * Perform a request
     *
     * @param string $method
     * @param string $path
     * @param array  $options
     * @return ResponseInterface
     */
    public function sendRequest($method, $path, array $options = [])
    {
        try {
            return $this->guzzleClient->request($method, $path, $options);
        }
        catch (BadResponseException $e) {

            throw ($e->getResponse()->hasHeader('x-ddn-status'))
                ? new WosException((int) $e->getResponse()->getHeaderLine('x-ddn-status'))
                : $e;
        }
    }

    /**
     * Process a response
     *
     * Ensure that this is a valid response, and
     * convert any DDN errors into appropriate exceptions
     *
     * @param ResponseInterface $response
     */
    private function checkResponse(ResponseInterface $response)
    {
        if (! $response->hasHeader('x-ddn-status')) {
            throw new MissingRequiredHeaderException('x-ddn-status');
        }

        if ($response->getHeaderLine('x-ddn-status'){0} !== '0') {
            throw new WosException((int) $response->getHeaderLine('x-ddn-status'));
        }
    }


}
