<?php

namespace Cyclonecode\NewsDataIO\Tests;

use PHPUnit\Framework\TestCase;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\NewsDataIO;

define('ABSPATH', __DIR__);

global $option;
global $currentUserCan;
$option = [
    NewsDataIO::OPTION_NAME => [
        Arguments::ARG_APIKEY => getenv('API_TOKEN'),
    ],
];
$currentUserCan = false;

abstract class AbstractTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        \Brain\Monkey\Functions\stubEscapeFunctions();
        \Brain\Monkey\Functions\stubTranslationFunctions();
        \Brain\Monkey\Functions\stubs([
            'get_option' => function (string $key, $default = null) {
                global $option;
                return $option[$key] ?? $default;
            },
            'update_option' => function (string $key, $value) {
                global $option;
                $option[$key] = $value;
                return true;
            },
            'delete_option' => function (string $key) {
                global $option;
                unset($option[$key]);
            },
            'load_plugin_textdomain' => false,
            'add_action' => function (string $action, $function) {

            },
            'add_shortcode' => function (string $shortcode, $function) {

            },
            'admin_url' => function (string $url) {
                return $url;
            },
            'plugin_dir_url' => function (string $url) {
                return $url;
            },
            'locate_template' => '',
            'wp_register_style' => '',
            'wp_enqueue_style' => '',
            'add_menu_page' => '',
            'checked' => '',
            'selected' => '',
            'submit_button' => '',
            'wp_nonce_field' => '',
            'check_admin_referer' => true,
            'wp_get_referer' => '',
            'current_user_can' => function () {
                global $currentUserCan;
                return $currentUserCan;
            },
            'wp_safe_redirect' => '',
            'wp_die' => function (string $text) {
                echo $text;
            },
            'get_current_screen' => function () {
                $screen = new \stdClass();
                $screen->id = 'toplevel_page_newsdata-io';
                return $screen;
            },
            'wp_json_encode' => fn ($data) => json_encode($data),
        ]);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
