<?php


namespace Synetic\JsonApiClient\Interfaces;

use GuzzleHttp\Psr7\Response;

interface ResponseParserInterface {

  /**
   * @param \GuzzleHttp\Psr7\Response $response
   *
   * @return bool
   */
  public function responseHasErrors(Response $response);

  /**
   * @param int $httpCode
   * @param int|string $applicationCode
   * @param string $message
   *
   * @return \GuzzleHttp\Psr7\Response
   */
  public function createErrorResponse($httpCode, $applicationCode, $message);
}
