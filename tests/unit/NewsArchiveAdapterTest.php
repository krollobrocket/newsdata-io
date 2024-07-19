<?php

namespace Cyclonecode\NewsDataIO\Tests\Unit;

use Cyclonecode\NewsDataIO\Adapters\NewsArchiveAdapter;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Tests\AbstractTestCase;
use GuzzleHttp\Client;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;

class NewsArchiveAdapterTest extends AbstractTestCase
{
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();
        $client = new Client();
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $this->sut = new NewsArchiveAdapter($settings, $client);
    }

    public function testGetNews(): void
    {
        $response = $this->sut->getNews();
        $this->assertNull($response);
    }

    public function testGetNewsWithFromDate(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_FROM_DATE => '2000-01-01',
        ]);
        $this->assertNull($response);
    }

    public function testGetNewsWithInvalidFromDate(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_FROM_DATE => 'bogus',
        ]);
        $this->assertNull($response);
    }

    public function testGetNewsWithToDate(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_TO_DATE => '2000-01-01',
        ]);
        $this->assertNull($response);
    }

    public function testGetNewsWithInvalidToDate(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_TO_DATE => 'bogus',
        ]);
        $this->assertNull($response);
    }

    public function testGetAllowedArguments(): void
    {
        $this->assertIsArray($this->sut->getAllowedArguments());
    }
}
