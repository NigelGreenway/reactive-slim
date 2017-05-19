<?php
declare(strict_types=1);
namespace ReactiveSlim\Test\Integration;

use GuzzleHttp\Client;
use Slim\App;
use PHPUnit\Framework\TestCase;


/**
 * @final
 */
class Bootstrap extends TestCase
{
    /** @var  App    $app */
    protected $app;
    /** @var  Client $client */
    protected $client;


    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = new Client();
    }

    /**
     * @param string $url
     * @return string
     */
    final public function makeURL(string $url) :string
    {
        return sprintf(
            '0.0.0.0:1351/%s',
            $url
        );
    }
}
