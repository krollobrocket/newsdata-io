<?php

use Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;
use PHPUnit\Framework\TestCase;

$option = [
    NewsDataIO::OPTION_NAME => [
        Arguments::ARG_APIKEY => 'pub_39467bf0d0fda18e6194809c1545718b1bb04',
    ],
];

$currentUserCan = true;

define('ABSPATH', __DIR__);

function wp_json_encode($data)
{
    return \json_encode($data);
}

function load_plugin_textdomain()
{

}

function wp_die($text)
{
    echo $text;
}

function wp_safe_redirect()
{

}

function wp_get_referer()
{

}

function wp_enqueue_style()
{

}

function get_option(string $key, $default = null) {
    global $option;
    return $option[$key] ?? $default;
}

function update_option(string $key, $value)
{
    global $option;
    $option[$key] = $value;
    return true;
}

function delete_option(string $key)
{
    global $option;
    unset($option[$key]);
}

function add_action()
{

}

function add_shortcode()
{

}

function locate_template()
{

}

function check_admin_referer()
{

}

function esc_url(string $url)
{
    return $url;
}

function esc_attr(?string $text)
{
    return $text;
}

function esc_attr_e(string $text)
{
    echo $text;
}

function __(string $text)
{
    return $text;
}

function current_user_can()
{
    global $currentUserCan;
    return $currentUserCan;
}

class NewsDataIOTest extends TestCase
{
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();
        $client = new Client();
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $adapter = new LatestNewsAdapter($settings, $client);
        $this->sut = new Cyclonecode\NewsDataIO\NewsDataIO($settings);
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
