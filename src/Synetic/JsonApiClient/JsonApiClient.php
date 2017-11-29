<?php


namespace Synetic\JsonApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Synetic\JsonApiClient\Interfaces\BodyStreamFactoryInterface;
use Synetic\JsonApiClient\Interfaces\ResponseParserInterface;

class JsonApiClient {

  /**
   * Http guzzle client.
   *
   * @var \GuzzleHttp\Client
   */
  private $client;

  /**
   * Response parser.
   *
   * @var \Synetic\JsonApiClient\Interfaces\ResponseParserInterface
   */
  private $responseParser;

  /**
   * Body stream factory.
   *
   * @var \Synetic\JsonApiClient\Interfaces\BodyStreamFactoryInterface
   */
  private $bodyStreamFactory;

  /**
   * JsonApiClient constructor.
   *
   * @param \GuzzleHttp\Client $client
   *   The http client.
   * @param \Synetic\JsonApiClient\Interfaces\ResponseParserInterface $responseParser
   *   The response parser.
   * @param \Synetic\JsonApiClient\Interfaces\BodyStreamFactoryInterface $bodyStreamFactory
   *   The body stream factory.
   */
  public function __construct(
    Client $client,
    ResponseParserInterface $responseParser,
    BodyStreamFactoryInterface $bodyStreamFactory
  ) {
    $this->client = $client;
    $this->responseParser = $responseParser;
    $this->bodyStreamFactory = $bodyStreamFactory;
  }

  /**
   * Execute a get request.
   *
   * @param string $uri
   *   The uri to request.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   *   The response.
   */
  public function get($uri, ParameterBag $parameters) {
    return $this->executeRequestWithoutBody('get', $uri, $parameters);
  }

  /**
   * Execute a post request.
   *
   * @param string $uri
   *   The url to request.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   *   The body.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   *   The response.
   */
  public function post($uri, ParameterBag $body, ParameterBag $parameters) {
    return $this->executeRequestWithBody('post', $uri, $body, $parameters);
  }

  /**
   * Execute a put request.
   *
   * @param string $uri
   *   The uri to request.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   *   The body.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   *   The response.
   */
  public function put($uri, ParameterBag $body, ParameterBag $parameters) {
    return $this->executeRequestWithBody('put', $uri, $body, $parameters);

  }

  /**
   * Execute a patch request.
   *
   * @param string $uri
   *   The uri to request.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   *   The body.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   *   The response.
   */
  public function patch($uri, ParameterBag $body, ParameterBag $parameters) {
    return $this->executeRequestWithBody('patch', $uri, $body, $parameters);
  }

  /**
   * Execute the request.
   *
   * @param string $method
   *   The method type.
   * @param string $uri
   *   The uri.
   * @param array $options
   *   The request options
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   The response.
   */
  protected function executeRequest($method, $uri, array $options) {
    try {
      $response = $this->client->request($method, $uri, $options);
    } catch (ClientException $e) {
      return $this->responseParser->createErrorResponse($e->getCode(), '', $e->getMessage());
    }

    if ($this->responseParser->responseHasErrors($response)) {
      $response->getBody()->rewind();
      $body = json_decode($response->getBody()->getContents(), true);

      $error = array_shift($body['errors']);

      return $this->responseParser->createErrorResponse(
        $response->getStatusCode(),
        isset($error['code']) ? $error['code'] : '',
        isset($error['details']) ? $error['details'] : ''
        );
    }

    return $response;
  }

  /**
   * Execute a request without a body.
   *
   * @param string $method
   *   The request method.
   * @param string $uri
   *   The uri.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   The response.
   */
  protected function executeRequestWithoutBody($method, $uri, ParameterBag $parameters) {
    $options = $this->buildOptions($parameters);

    return $this->executeRequest($method, $uri, $options);
  }

  /**
   * Execute a request with a body.
   *
   * @param string $method
   *   The request method.
   * @param string $uri
   *   The uri.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   *   The body contents.
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   The response.
   */
  protected function executeRequestWithBody($method, $uri, ParameterBag $body, ParameterBag $parameters) {
    $options = $this->buildOptions($parameters);
    $options['body'] = $this->bodyStreamFactory->createStream($body);

    return $this->executeRequest($method, $uri, $options);
  }

  /**
   * Build up the request options array.
   *
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *   The parameters.
   *
   * @return array
   *   The options array
   */
  protected function buildOptions(ParameterBag $parameters) {
    return [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => $parameters->all(),
    ];
  }

}
