<?php


namespace Synetic\JsonApiClient;


use GuzzleHttp\Client;

class Factory {

  /**
   * Create a new Json api client instance.
   *
   * @return \Synetic\JsonApiClient\JsonApiClient
   *   Json Api client.
   */
  public static function create() {
    return new JsonApiClient(
      new Client(),
      new ResponseParser(),
      new BodyStreamFactory()
    );
  }
}
