<?php

namespace Synetic\JsonApiClient;

use GuzzleHttp\Psr7\Stream;
use Prophecy\Prophet;
use Symfony\Component\HttpFoundation\ParameterBag;

class BodyStreamFactoryTest extends \PHPUnit_Framework_TestCase {

  /**
   * The parameter bag.
   *
   * @var Prophet|ParameterBag
   */
  private $parameterBag;

  /**
   * Set up the environment.
   */
  public function setUp() {
    $this->parameterBag = $this->prophesize(ParameterBag::class);
  }

  /**
   * Test the stream creation.
   */
  public function testCreateStream() {
    $factory = new BodyStreamFactory();

    $this->parameterBag->all()->shouldBeCalled()
      ->willreturn([]);

    $result = $factory->createStream($this->parameterBag->reveal());

    $this->assertInstanceOf(
      Stream::class,
      $result
    );
  }

}
