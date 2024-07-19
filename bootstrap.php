<?php

/**
 * Plugin Name: News Data IO
 * Description: Query newsdata.io for latest news.
 * Version: 1.0.0
 * Requires at least: 4.0.0
 * Requires PHP: 7.4
 * Tested up to: 6.6.1
 * Author: Cyclonecode
 * Author URI: https://stackoverflow.com/users/1047662/cyclonecode?tab=profile
 * Copyright: Cyclonecode
 * License: GPLv2 or later
 * Text Domain: newsdata-io
 * Domain Path: /languages
 *
 * @author Cyclonecode
 */

namespace Cyclonecode\NewsDataIO;

require_once __DIR__ . '/vendor/autoload.php';

use Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;

add_action('plugins_loaded', function () {
    $client = new \GuzzleHttp\Client();
    $settings = new Settings(NewsDataIO::OPTION_NAME);
    $io = new NewsDataIO($settings);
    $adapter = new LatestNewsAdapter($settings, $client);
    $io->setAdapter($adapter);
});

register_uninstall_hook(__FILE__, NewsDataIO::class . '::delete');
