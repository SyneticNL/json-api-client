<?php


namespace Synetic\JsonApiClient\Interfaces;


use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Interface BodyStreamFactoryInterface.
 *
 * @package Synetic\JsonApiClient\Interfaces
 */
interface BodyStreamFactoryInterface {

  /**
   * @param \Symfony\Component\HttpFoundation\ParameterBag $parameterBag
   *
   * @return \GuzzleHttp\Psr7\Stream
   */
  public function createStream(ParameterBag $parameterBag);
}
