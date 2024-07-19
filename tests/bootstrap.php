<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/AbstractTestCase.php';

use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\NewsDataIO;

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

function add_menu_page()
{

}

function get_current_screen()
{
    $screen = new \stdClass();
    $screen->id = 'toplevel_page_newsdata-io';
    return $screen;
}

function wp_register_style()
{

}

function plugin_dir_url()
{

}

function admin_url(string $path)
{
    return $path;
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

function wp_nonce_field()
{

}

function checked()
{

}

function selected()
{

}

function submit_button()
{

}

function esc_textarea(string $text)
{
    return $text;
}

function esc_url(string $url)
{
    return $url;
}

function esc_attr(?string $text)
{
    return $text;
}

function esc_attr__(?string $text)
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

function _e(string $text)
{
    return $text;
}

function current_user_can()
{
    global $currentUserCan;
    return $currentUserCan;
}
