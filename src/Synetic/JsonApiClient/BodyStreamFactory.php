<?php


namespace Synetic\JsonApiClient;

use GuzzleHttp\Psr7;
use Symfony\Component\HttpFoundation\ParameterBag;
use Synetic\JsonApiClient\Interfaces\BodyStreamFactoryInterface;

class BodyStreamFactory implements BodyStreamFactoryInterface {

  /**
   * {@inheritdoc}
   */
  public function createStream(ParameterBag $data) {
    return Psr7\stream_for(json_encode($data->all()));
  }

}
