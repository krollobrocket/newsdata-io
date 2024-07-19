<?php

namespace Cyclonecode\NewsDataIO;

use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Interfaces\NewsApiInterface;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;

class NewsDataIO
{
    const OPTION_NAME = 'newsdata-io-settings';
    const VERSION = '1.0.0';

    protected Settings $settings;
    protected ?NewsApiInterface $adapter;

    public function __construct(Settings $settings)
    {
        $this->localize();
        $this->settings = $settings;
        add_action('admin_menu', [$this, 'adminMenu']);
        add_action('in_admin_header', [$this, 'adminHeader']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
        add_action('admin_post_newsdata_io_save_settings', fn () => $this->saveSettings($_POST));
        add_shortcode('newsdata-io', [$this, 'renderShortCode']);
    }

    public function enqueueAssets(): void
    {
        wp_register_style(
            'newsdata-io',
            plugin_dir_url(__FILE__) . 'assets/css/style.css',
            [],
            self::VERSION
        );
    }

    public function setAdapter(NewsApiInterface $adapter): void
    {
        $this->adapter = $adapter;
    }

    protected function localize(): void
    {
        load_plugin_textdomain('newsdata-io', false, 'newsdata-io/languages');
    }

    public function renderShortCode(array $attributes = []): string
    {
        wp_enqueue_style('newsdata-io');
        $result = $this->adapter->getNews($attributes);
        if (!$result) {
            return '';
        }
        $template = locate_template([
            'news.php',
        ]);
        if (!$template) {
            $template = __DIR__ . '/templates/news.php';
        }
        extract([
            'result' => $result,
        ]);
        ob_start();

        include $template;
        // load_template($template, true, ['result' => $result]);
        return ob_get_clean();
    }

    public function saveSettings(array $args)
    {
        // Verify nonce and referer.
        check_admin_referer('newsdata-io-settings-action', 'newsdata-io-settings-nonce');

        // Validate so user has correct privileges.
        if (!current_user_can('manage_options')) {
            wp_die(esc_attr('You are not allowed to perform this action.', 'newsdata-io'));
        }

        $this->settings->set(Arguments::ARG_APIKEY, filter_var($args[Arguments::ARG_APIKEY] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_PREMIUM_APIKEY, filter_var($args[Arguments::ARG_PREMIUM_APIKEY] ?? '', FILTER_VALIDATE_BOOLEAN));
        $this->settings->set(Arguments::ARG_SIZE, filter_var($args[Arguments::ARG_SIZE] ?? '', FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
                'max_range' => $this->settings->get(Arguments::ARG_PREMIUM_APIKEY) ? Arguments::MAX_SIZE_PREMIUM : Arguments::MAX_SIZE_FREE,
                'default' => 5,
            ],
        ]));
        // @todo: Add select element where the user can choose either Query, Query Title or Query Meta,
        //   this needs to be accompanied with a text control for the actual query string.
        $this->settings->set(Arguments::ARG_QUERY, filter_var($args[Arguments::ARG_QUERY] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_QUERY_TITLE, filter_var($args[Arguments::ARG_QUERY_TITLE] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_QUERY_META, filter_var($args[Arguments::ARG_QUERY_META] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_IMAGE, filter_var($args[Arguments::ARG_IMAGE] ?? '', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE));
        $this->settings->set(Arguments::ARG_VIDEO, filter_var($args[Arguments::ARG_VIDEO] ?? '', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE));
        $countries = explode(',', filter_var($args[Arguments::ARG_COUNTRY] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_COUNTRY, $this->sanitizeArray($countries, $this->adapter->getCountryCodes()));
        $categories = explode(',', filter_var($args[Arguments::ARG_CATEGORY] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_CATEGORY, $this->sanitizeArray($categories, $this->adapter->getCategoryCodes()));
        $categories = explode(',', filter_var($args[Arguments::ARG_EXCLUDE_CATEGORY] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_EXCLUDE_CATEGORY, $this->sanitizeArray($categories, $this->adapter->getCategoryCodes()));
        $languages = explode(',', filter_var($args[Arguments::ARG_LANGUAGE] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->set(Arguments::ARG_LANGUAGE, $this->sanitizeArray($languages, $this->adapter->getLanguageCodes()));
        $domains = explode(',', filter_var($args[Arguments::ARG_DOMAIN] ?? '', FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
        $this->settings->set(Arguments::ARG_DOMAIN, $this->sanitizeArray($domains));
        $domains = explode(',', filter_var($args[Arguments::ARG_DOMAIN_URL] ?? '', FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
        $this->settings->set(Arguments::ARG_DOMAIN_URL, $this->sanitizeArray($domains));
        $domains = explode(',', filter_var($args[Arguments::ARG_EXCLUDE_DOMAIN] ?? '', FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
        $this->settings->set(Arguments::ARG_EXCLUDE_DOMAIN, $this->sanitizeArray($domains));
        $priorityDomain = filter_var($args[Arguments::ARG_PRIORITY_DOMAIN] ?? '', FILTER_CALLBACK, [
            'options' => fn (string $value) => in_array($value, ['top', 'medium', 'low']),
        ]);
        $this->settings->set(Arguments::ARG_PRIORITY_DOMAIN, $priorityDomain ? $args[Arguments::ARG_PRIORITY_DOMAIN] : null);
        $this->settings->set(Arguments::ARG_TIMEZONE, filter_var($args[Arguments::ARG_TIMEZONE] ?? '', FILTER_UNSAFE_RAW));
        $sentiment = filter_var($args[Arguments::ARG_SENTIMENT] ?? '', FILTER_CALLBACK, [
            'options' => fn (string $value) => in_array($value, ['positive', 'negative', 'neutral']),
        ]);
        $this->settings->set(Arguments::ARG_SENTIMENT, $sentiment ? $args[Arguments::ARG_SENTIMENT] : null);
        // $this->settings->set(Arguments::ARG_TIMEFRAME, filter_var($args[Arguments::ARG_TIMEFRAME] ?? '', FILTER_UNSAFE_RAW));
        $this->settings->save();

        wp_safe_redirect(wp_get_referer());
    }

    protected function getTimeZones(): array
    {
        $timezones = timezone_identifiers_list();
        return $timezones;
    }

    protected function sanitizeArray(array $array, array $allowedValues = []): string
    {
        $array = array_map('trim', $array);
        $array = array_filter($array);
        $array = array_unique($array);
        if ($allowedValues) {
            $array = array_filter($array, fn (string $item) => in_array($item, $allowedValues));
        }
        $array = array_slice($array, 0, 5);
        return implode(',', $array);
    }

    public function adminHeader(): void
    {
        if (get_current_screen()->id !== 'toplevel_page_newsdata-io') {
            return;
        }
        ?>
        <style type="text/css">
            #newsdata-io-admin-header {
                padding-top: 20px;
            }
            #newsdata-io-admin-header h1 {
                font-size: 23px;
                font-weight: 400;
                margin-top: 20px;
            }
            #newsdata-io-admin-header img {
                float: left;
                margin-right: 20px;
                rotate: 180deg;
                filter: grayscale(100%);
            }
        </style>
        <div id="newsdata-io-admin-header">
            <span><img width="64" src="<?php echo plugin_dir_url(__FILE__); ?>assets/images/newsdata-icon.png" alt="<?php _e('Newsdata IO', 'newsdata-io'); ?>" />
                <h1><?php _e('Newsdata IO', 'newsdata-io'); ?></h1>
            </span>
        </div>
        <?php
    }

    public function adminMenu(): void
    {
        add_menu_page(
            __('Newsdata IO', 'newsdata-io'),
            __('Newsdata IO', 'newsdata-io'),
            'manage_options',
            'newsdata-io',
            [$this, 'adminPage']
        );
    }

    public function adminPage(): void
    {
        require_once __DIR__ . '/templates/settings.php';
    }

    public static function uninstall(): void
    {
        delete_option(self::OPTION_NAME);
    }
}
