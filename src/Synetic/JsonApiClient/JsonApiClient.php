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
   * @param \Synetic\JsonApiClient\Interfaces\ResponseParserInterface $responseParser
   * @param \Synetic\JsonApiClient\Interfaces\BodyStreamFactoryInterface $bodyStreamFactory
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
   * @param string $uri
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   */
  public function get($uri, ParameterBag $parameters) {
    return $this->executeRequestWithoutBody('get', $uri, $parameters);
  }

  /**
   * @param string $uri
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   */
  public function post($uri, ParameterBag $body, ParameterBag $parameters) {
    return $this->executeRequestWithBody('post', $uri, $body, $parameters);
  }

  /**
   * @param string $uri
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   */
  public function put($uri, ParameterBag $body, ParameterBag $parameters) {
    return $this->executeRequestWithBody('put', $uri, $body, $parameters);

  }

  /**
   * @param string $uri
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return mixed|\Psr\Http\Message\ResponseInterface
   */
  public function patch($uri, ParameterBag $body, ParameterBag $parameters) {
    return $this->executeRequestWithBody('patch', $uri, $body, $parameters);
  }

  /**
   * @param string $method
   * @param string $uri
   * @param array $options
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function executeRequest($method, $uri, array $options) {
    try {
      $response = $this->client->request($method, $uri, $options);
    } catch (ClientException $e) {
      return $this->responseParser->createErrorResponse($e->getCode(), '', $e->getMessage());
    }

    if ($this->responseParser->responseHasErrors($response)) {
      $response->getBody()->rewind();
      $body = json_decode($response->getBody()->getContents());

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
   * @param string $method
   * @param string $uri
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function executeRequestWithoutBody($method, $uri, ParameterBag $parameters) {
    $options = $this->buildOptions($parameters);

    return $this->executeRequest($method, $uri, $options);
  }

  /**
   * @param string $method
   * @param string $uri
   * @param \Symfony\Component\HttpFoundation\ParameterBag $body
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function executeRequestWithBody($method, $uri, ParameterBag $body, ParameterBag $parameters) {
    $options = $this->buildOptions($parameters);
    $options['body'] = $this->bodyStreamFactory->createStream($body);

    return $this->executeRequest($method, $uri, $options);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameters
   *
   * @return array
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
