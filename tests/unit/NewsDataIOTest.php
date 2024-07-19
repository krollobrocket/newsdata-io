<?php

namespace Cyclonecode\NewsDataIO\Tests\Unit;

use Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Tests\AbstractTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;

class NewsDataIOTest extends AbstractTestCase
{
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();
        $client = new Client();
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $adapter = new LatestNewsAdapter($settings, $client);
        $this->sut = new NewsDataIO($settings);
        $this->sut->setAdapter($adapter);
        global $currentUserCan;
        $currentUserCan = true;
    }

    public function testUserCan(): void
    {
        global $currentUserCan;
        $currentUserCan = false;
        ob_start();
        $this->testSaveSettings([]);
        $response = ob_get_clean();
        $this->assertEquals('You are not allowed to perform this action.', $response);
    }

    public function testSaveSettings(): void
    {
        global $option;
        $args = [
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_LANGUAGE => 'en',
            Arguments::ARG_APIKEY => 'foobar',
            Arguments::ARG_PREMIUM_APIKEY => false,
            Arguments::ARG_CATEGORY => '',
            Arguments::ARG_EXCLUDE_CATEGORY => '',
            Arguments::ARG_COUNTRY => '',
            Arguments::ARG_IMAGE => 1,
            Arguments::ARG_VIDEO => 0,
            Arguments::ARG_QUERY => '',
            Arguments::ARG_QUERY_TITLE => '',
            Arguments::ARG_QUERY_META => '',
            Arguments::ARG_DOMAIN => '',
            Arguments::ARG_DOMAIN_URL => '',
            Arguments::ARG_PRIORITY_DOMAIN => null,
            Arguments::ARG_EXCLUDE_DOMAIN => '',
            Arguments::ARG_SENTIMENT => null,
            Arguments::ARG_TIMEZONE => '',
        ];
        $this->sut->saveSettings($args);
        $this->assertEquals($args, $option['newsdata-io-settings']);
    }

    public function testRenderShortCode(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->renderShortCode([
            Arguments::ARG_SIZE => 5,
        ]);
        $this->assertIsString($response);
    }

    public function testEnqueueAssets(): void
    {
        $this->sut->enqueueAssets();
        $this->assertTrue(true);
    }

    public function testAdminHeader(): void
    {
        ob_start();
        $this->sut->adminHeader();
        $response = ob_get_clean();
        $this->assertIsString($response);
    }

    public function testAdminMenu(): void
    {
        $this->sut->adminMenu();
        $this->assertTrue(true);
    }

    public function testAdminPageTabLatest(): void
    {
        ob_start();
        $this->sut->adminPage();
        ob_end_clean();
        $this->assertTrue(true);
    }

    public function testUninstall(): void
    {
        NewsDataIO::uninstall();
        $this->assertTrue(true);
    }

    public function testGetTimeZones(): void
    {
        $this->assertIsArray($this->invokeMethod($this->sut, 'getTimeZones'));
    }

    protected function createAndSetClient(?string $fileName): void
    {
        $body = file_get_contents(__DIR__ . '/data/' . $fileName);
        $mock = new MockHandler([new Response(200, [], $body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler,
        ]);
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $adapter = new LatestNewsAdapter($settings, $client);
        $this->sut->setAdapter($adapter);
    }
}
