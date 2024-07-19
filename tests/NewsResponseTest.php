<?php

use Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Responses\NewsResponse;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;
use PHPUnit\Framework\TestCase;

class NewsResponseTest extends TestCase
{
    protected NewsResponse $response;

    public function setUp(): void
    {
        parent::setUp();
        $body = file_get_contents(__DIR__ . '/data/latest-size-5.json');
        $mock = new MockHandler([new Response(200, [], $body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler,
        ]);
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $adapter = new LatestNewsAdapter($settings, $client);
        $this->response = $adapter->getNews();
    }

    public function testGetStatus(): void
    {
        $this->assertEquals('success', $this->response->getStatus());
    }

    public function testGetResults(): void
    {
        $this->assertIsArray($this->response->getResults());
    }

    public function testGetTotalResults(): void
    {
        $this->assertIsInt($this->response->getTotalResults());
    }

    public function testGetNextPage(): void
    {
        $this->assertIsString($this->response->getNextPage());
    }
}
