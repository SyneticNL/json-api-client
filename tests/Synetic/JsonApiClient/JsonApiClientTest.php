<?php

namespace Synetic\JsonApiClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Synetic\JsonApiClient\Interfaces\BodyStreamFactoryInterface;
use Synetic\JsonApiClient\Interfaces\ResponseParserInterface;

class JsonApiClientTest extends \PHPUnit_Framework_TestCase {

  /**
   * The guzzle http client mock.
   *
   * @var Prophet|Client
   */
  private $httpClient;

  /**
   * The response parser interface mock.
   *
   * @var Prophet|ResponseParserInterface
   */
  private $responseParser;

  /**
   * The body stream interface mock.
   *
   * @var Prophet|\Synetic\JsonApiClient\BodyStreamFactory
   */
  private $bodyStreamFactory;

  /**
   * The parameters mock.
   *
   * @var Prophet|ParameterBag
   */
  private $parameters;

  /**
   * The body mock.
   *
   * @var Prophet|ParameterBag
   */
  private $body;

  /**
   * The response mock.
   *
   * @var Prophet|Response
   */
  private $response;

  public function setUp() {
    $this->httpClient = $this->prophesize(Client::class);
    $this->responseParser = $this->prophesize(ResponseParserInterface::class);
    $this->bodyStreamFactory = $this->prophesize(BodyStreamFactoryInterface::class);
    $this->parameters = $this->prophesize(ParameterBag::class);
    $this->body = $this->prophesize(ParameterBag::class);
    $this->response = $this->prophesize(Response::class);
  }

  public function testGet() {
    $uri = '/test';
    $options = [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => [
        'foo' => 'bar',
      ],
    ];

    $expected = $this->response->reveal();

    $this->parameters->all()
      ->shouldBeCalled()
      ->willReturn($options['query']);

    $this->httpClient->request('get', $uri, $options)
      ->shouldBeCalled()
      ->willReturn($expected);

    $client = new JsonApiClient(
      $this->httpClient->reveal(),
      $this->responseParser->reveal(),
      $this->bodyStreamFactory->reveal()
    );

    $result = $client->get($uri, $this->parameters->reveal());

    $this->assertSame(
      $expected,
      $result
    );
  }

  public function testPost() {
    $stream = $this->prophesize(StreamInterface::class);
    $this->bodyStreamFactory->createStream($this->body)
      ->shouldBeCalled()
      ->willReturn($stream);

    $uri = '/test';
    $options = [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => [
        'foo' => 'bar',
      ],
      'body' => $stream,
    ];

    $expected = $this->response->reveal();

    $this->parameters->all()
      ->shouldBeCalled()
      ->willReturn($options['query']);

    $this->httpClient->request('post', $uri, $options)
      ->shouldBeCalled()
      ->willReturn($expected);

    $client = new JsonApiClient(
      $this->httpClient->reveal(),
      $this->responseParser->reveal(),
      $this->bodyStreamFactory->reveal()
    );

    $result = $client->post($uri, $this->body->reveal(), $this->parameters->reveal());

    $this->assertSame(
      $expected,
      $result
    );
  }

  public function testPatch() {
    $stream = $this->prophesize(StreamInterface::class);
    $this->bodyStreamFactory->createStream($this->body)
      ->shouldBeCalled()
      ->willReturn($stream);

    $uri = '/test';
    $options = [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => [
        'foo' => 'bar',
      ],
      'body' => $stream,
    ];

    $expected = $this->response->reveal();

    $this->parameters->all()
      ->shouldBeCalled()
      ->willReturn($options['query']);

    $this->httpClient->request('patch', $uri, $options)
      ->shouldBeCalled()
      ->willReturn($expected);

    $client = new JsonApiClient(
      $this->httpClient->reveal(),
      $this->responseParser->reveal(),
      $this->bodyStreamFactory->reveal()
    );

    $result = $client->patch($uri, $this->body->reveal(), $this->parameters->reveal());

    $this->assertSame(
      $expected,
      $result
    );
  }

  public function testPut() {
    $stream = $this->prophesize(StreamInterface::class);
    $this->bodyStreamFactory->createStream($this->body)
      ->shouldBeCalled()
      ->willReturn($stream);

    $uri = '/test';
    $options = [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => [
        'foo' => 'bar',
      ],
      'body' => $stream,
    ];

    $expected = $this->response->reveal();

    $this->parameters->all()
      ->shouldBeCalled()
      ->willReturn($options['query']);

    $this->httpClient->request('put', $uri, $options)
      ->shouldBeCalled()
      ->willReturn($expected);

    $client = new JsonApiClient(
      $this->httpClient->reveal(),
      $this->responseParser->reveal(),
      $this->bodyStreamFactory->reveal()
    );

    $result = $client->put($uri, $this->body->reveal(), $this->parameters->reveal());

    $this->assertSame(
      $expected,
      $result
    );
  }

  public function testRequestReturnsErrorResponseByClientException() {
    $uri = '/test';
    $options = [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => [
        'foo' => 'bar',
      ],
    ];

    $this->parameters->all()
      ->shouldBeCalled()
      ->willReturn($options['query']);

    $this->httpClient->request('get', $uri, $options)
      ->shouldBeCalled()
      ->willThrow(ClientException::class);

    $expected = $this->response->reveal();

    $this->responseParser->createErrorResponse(Argument::any(), Argument::any(), Argument::any())
      ->shouldBeCalled()
      ->willReturn($expected);

    $client = new JsonApiClient(
      $this->httpClient->reveal(),
      $this->responseParser->reveal(),
      $this->bodyStreamFactory->reveal()
    );

    $result = $client->get($uri, $this->parameters->reveal());

    $this->assertSame(
      $expected,
      $result
    );
  }

  public function testRequestReturnsErrorResponseByErrorContent() {
    $uri = '/test';
    $options = [
      'headers' => [
        'content-type' => 'application/vnd.api+json',
      ],
      'query' => [
        'foo' => 'bar',
      ],
    ];

    $this->parameters->all()
      ->shouldBeCalled()
      ->willReturn($options['query']);

    $this->httpClient->request('get', $uri, $options)
      ->shouldBeCalled()
      ->willReturn($this->response);

    $expected = $this->response->reveal();

    $this->responseParser->responseHasErrors($this->response)
      ->shouldBeCalled()
      ->willReturn(true);

    $body = $this->prophesize(StreamInterface::class);
    $this->response->getBody()
      ->shouldBeCalled()
      ->willReturn($body);

    $this->response->getStatusCode()
      ->shouldBeCalled()
      ->willReturn(1);

    $body->rewind()->shouldBeCalled();
    $body->getContents()->shouldBeCalled()
      ->willReturn(json_encode([
        'errors' => [
          [
            'code' => 1,
            'details' => 'error'
          ],
        ],
      ]));

    $this->responseParser->createErrorResponse(1, 1, 'error')
      ->shouldBeCalled()
      ->willReturn($expected);

    $client = new JsonApiClient(
      $this->httpClient->reveal(),
      $this->responseParser->reveal(),
      $this->bodyStreamFactory->reveal()
    );

    $result = $client->get($uri, $this->parameters->reveal());

    $this->assertSame(
      $expected,
      $result
    );
  }

}
