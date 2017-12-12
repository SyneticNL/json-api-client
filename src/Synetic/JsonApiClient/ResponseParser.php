<?php

namespace Synetic\JsonApiClient;

use GuzzleHttp\Psr7\Response;
use Synetic\JsonApiClient\Interfaces\ResponseParserInterface;

/**
 * Class ResponseParser.
 *
 * @package Synetic\JsonApiClient
 */
class ResponseParser implements ResponseParserInterface {

  /**
   * {@inheritdoc}
   */
  public function responseHasErrors(Response $response) {
    if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
      return true;
    }

    $response->getBody()->rewind();
    $body = json_decode($response->getBody()->getContents(), true);

    if (is_array($body) && array_key_exists('errors', $body)) {
      return true;
    }

    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function createErrorResponse($httpCode, $applicationCode, $message) {
    return new Response(
      $httpCode,
      [
        'content-type' => 'application/vnd.api+json',
      ],
      json_encode([
        'errors' => [
          'status' => $httpCode,
          'code' => $applicationCode,
          'detail' => $message,
        ],
      ])
    );
  }

}
