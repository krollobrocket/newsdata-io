<?php

namespace Cyclonecode\NewsDataIO\Tests\E2E;

use Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Responses\NewsResponse;
use Cyclonecode\NewsDataIO\Tests\AbstractTestCase;
use GuzzleHttp\Client;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;

class LatestNewsAdapterTest extends AbstractTestCase
{
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();
        $client = new Client();
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $this->sut = new LatestNewsAdapter($settings, $client);
    }

    public function testGetNewsError(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_TAG => 'crime',
        ]);
        $this->assertNull($response);
    }

    public function testGetNews(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }
}
