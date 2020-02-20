<?php
namespace CodeCloud\Bundle\ShopifyBundle\Api\Endpoint;

use CodeCloud\Bundle\ShopifyBundle\Api\Request\Exception\FailedRequestException;
use CodeCloud\Bundle\ShopifyBundle\Api\GenericResource;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\ResponseInterface;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\ErrorResponse;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\HtmlResponse;
use CodeCloud\Bundle\ShopifyBundle\Api\Response\JsonResponse;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Exception\GuzzleException;


abstract class AbstractEndpoint
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    protected $version;

    /**
     * Last response object
     * @var JsonResponse|HtmlResponse|ErrorResponse|null
     */
    private $response;


    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client, string $version)
    {
        $this->client = $client;
        $this->version = $version;
    }

    /**
     * Return last response object if any.
     * @return NULL|PsrResponse
     */
    public function getHttpResponse()
    {
        if ($this->response instanceof ResponseInterface) {
            return $this->response->getHttpResponse();
        }

        return $this->response;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws FailedRequestException
     */
    protected function send(RequestInterface $request)
    {
        $response = $this->process($request);

        if (! $response->successful()) {
            throw new FailedRequestException('Failed request. ' . $response->getHttpResponse()->getReasonPhrase(), $response->getHttpResponse()->getStatusCode());
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @param string $rootElement
     * @param array $links
     * @return array
     * @throws FailedRequestException
     */
    protected function sendPaged(RequestInterface $request, $rootElement, array &$links = array())
    {
        return $this->processPaged($request, $rootElement, array(), $links);
    }

    /**
     * @param array $items
     * @param GenericResource|null $prototype
     * @return array
     */
    protected function createCollection($items, GenericResource $prototype = null)
    {
        if (! $prototype) {
            $prototype = new GenericResource();
        }

        $collection = array();

        foreach ((array)$items as $item) {
            $newItem = clone $prototype;
            $newItem->hydrate($item);
            $collection[] = $newItem;
        }

        return $collection;
    }

    /**
     * @param array $data
     * @return GenericResource
     */
    protected function createEntity($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException(
                sprintf('Expected array, got "%s"', var_export($data, true)
            ));
        }

        $entity = new GenericResource();
        $entity->hydrate($data);

        return $entity;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function process(RequestInterface $request)
    {
        try {
            $guzzleResponse = $this->client->send($request);

            switch ($request->getHeaderLine('Content-type')) {
                case 'application/json':
                    $response = new JsonResponse($guzzleResponse);
                    break;
                default:
                    $response = new HtmlResponse($guzzleResponse);
            }
        } catch (GuzzleException $e) {
            $guzzleResponse = $e->getResponse();
            $response = new ErrorResponse($guzzleResponse, $e);
        } catch (ClientException $e) {
            $response = new ErrorResponse($guzzleResponse, $e);
        }

        $this->response = $response;

        return $this->response;
    }

    /**
     * Loop through a set of API results that are available in pages, returning the full resultset as one array
     * @param RequestInterface $request
     * @param string $rootElement
     * @param array $params
     * @param array $links
     * @return array
     */
    protected function processPaged(RequestInterface $request, $rootElement, array $params = array(), array &$links = array())
    {
        $requestUrl = $request->getUri();

        $parts = parse_url($requestUrl);

        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (array_key_exists('limit', $query)) {
                $response = $this->process($request->withUri(new Uri($requestUrl)));
                $links = $this->parseLinks($response);
                return $response->get($rootElement);
            }
        }

        $allResults = array();

        $callback = function($root) use (&$allResults){
            if ($pageResults = empty($root) ? false : $root) {
                $allResults = array_merge($allResults, $pageResults);
            }
        };

        $this->processChunk($request, $rootElement, $callback, $params);

        return $allResults;
    }


    /**
     * Loop through a set of API results that are available in pages, passing chunks to callback
     * @param RequestInterface $request
     * @param string $rootElement
     * @param callable $callback
     * @param array $params
     */
    protected function processChunk(RequestInterface $request, $rootElement, callable $callback, array $params = [])
    {
        if (empty($params['limit'])) {
            $params['limit'] = 250;
        }

        $requestUrl = $request->getUri();
        $paramDelim = strstr($requestUrl, '?') ? '&' : '?';
        $nextLink = $request->withUri(new Uri($requestUrl.$paramDelim.http_build_query($params)));

        do {
            $response = $this->process($nextLink);

            $root = $response->get($rootElement);

            $links = $this->parseLinks($response);
            if (isset($links['next'])) {
                $nextLink = $request->withUri(new Uri($links['next']));
            } else {
                $nextLink = null;
            }

            if ( ! empty($root)) {
                /**
                 * On each chunk result set, we will pass them to the callback and then let the
                 * developer take care of everything within the callback, which allows us to
                 * keep the memory low for spinning through large result sets for working.
                 */
                $callback($root);
            }

        } while ($nextLink);

    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function parseLinks(ResponseInterface $response)
    {
        $header = $response->getHttpResponse()->getHeaderLine('Link');
        $headerParts = explode(',', $header);

        $links = [];
        foreach ( $headerParts as $part) {
            if (preg_match('/<(.*)>; rel="(.*)"/', $part, $parsedHeader)) {
                $links[$parsedHeader[2]] = $parsedHeader[1];
            }
        }

        return $links;
    }
}
