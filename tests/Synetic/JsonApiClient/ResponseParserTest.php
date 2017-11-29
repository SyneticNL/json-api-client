<?php

namespace Synetic\JsonApiClient;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;

/**
 * Class ResponseParserTest.
 *
 * @package Synetic\JsonApiClient
 */
class ResponseParserTest extends \PHPUnit_Framework_TestCase {

  /**
   * The response mock.
   *
   * @var Prophet|Response
   */
  private $response;

  /**
   * The response parser.
   *
   * @var \Synetic\JsonApiClient\ResponseParser
   */
  private $responseParser;

  public function setUp() {
    $this->response = $this->prophesize(Response::class);
    $this->responseParser = new ResponseParser();
  }

  public function testResponseHasErrorsErrorByStatusCode() {
    $this->response->getStatusCode()
      ->shouldBeCalled()
      ->willReturn(400);

    $this->assertTrue(
      $this->responseParser->responseHasErrors($this->response->reveal())
    );
  }

  public function testResponseHasErrorsErrorByContent() {
    $this->response->getStatusCode()
      ->shouldBeCalled()
      ->willReturn(200);

    $body = $this->prophesize(StreamInterface::class);
    $body->rewind()->shouldBeCalled();
    $body->getContents()
      ->shouldBeCalled()
      ->willReturn(json_encode([
        'errors' => [
          [
            'code' => 1,
          ],
        ],
      ]));
    $this->response->getBody()
      ->shouldBeCalled()
      ->willReturn($body);

    $this->assertTrue(
      $this->responseParser->responseHasErrors($this->response->reveal())
    );
  }

  public function testResponseHasErrorsNoError() {
    $this->response->getStatusCode()
      ->shouldBeCalled()
      ->willReturn(200);

    $body = $this->prophesize(StreamInterface::class);
    $body->rewind()->shouldBeCalled();
    $body->getContents()
      ->shouldBeCalled()
      ->willReturn(json_encode([
        'data' => [
          [
            'code' => 1,
          ],
        ],
      ]));
    $this->response->getBody()
      ->shouldBeCalled()
      ->willReturn($body);

    $this->assertFalse(
      $this->responseParser->responseHasErrors($this->response->reveal())
    );
  }

}
