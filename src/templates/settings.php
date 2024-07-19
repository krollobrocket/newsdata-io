<?php

use Cyclonecode\NewsDataIO\Enums\Arguments;

?>
<div class="wrap">
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="POST">
        <?php wp_nonce_field('newsdata-io-settings-action', 'newsdata-io-settings-nonce'); ?>
        <input type="hidden" name="action" value="newsdata_io_save_settings" />
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_APIKEY); ?>"><?php esc_attr_e('Api Key', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text code" name="<?php echo esc_attr(Arguments::ARG_APIKEY); ?>" id="<?php echo esc_attr(Arguments::ARG_APIKEY); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_APIKEY)); ?>" />
                    <p class="description"><?php esc_attr_e('Api Key. This can either be a free or a premium key', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_PREMIUM_APIKEY); ?>"><?php esc_attr_e('Premium Api Key', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="checkbox" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_PREMIUM_APIKEY); ?>" id="<?php echo esc_attr(Arguments::ARG_PREMIUM_APIKEY); ?>"<?php checked($this->settings->get(Arguments::ARG_PREMIUM_APIKEY)); ?> />
                    <p class="description"><?php esc_attr_e('Premium Api Key.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_SIZE); ?>"><?php esc_attr_e('Number of articles', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="number" name="<?php echo esc_attr(Arguments::ARG_SIZE); ?>" id="<?php echo esc_attr(Arguments::ARG_SIZE); ?>" min="1" max="<?php echo esc_attr($this->settings->get('premiumApiKey')) ? 50 : 10; ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_SIZE)); ?>" />
                    <p class="description"><?php esc_attr_e('Maximum number of news articles.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_QUERY); ?>"><?php esc_attr_e('Query', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_QUERY); ?>" id="<?php echo esc_attr(Arguments::ARG_QUERY); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_QUERY)); ?>" />
                    <p class="description"><?php esc_attr_e('Search news articles for specific keywords or phrases present in the news title, content, URL, meta keywords and meta description.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_QUERY_TITLE); ?>"><?php esc_attr_e('Query Title', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_QUERY_TITLE); ?>" id="<?php echo esc_attr(Arguments::ARG_QUERY_TITLE); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_QUERY_TITLE)); ?>" />
                    <p class="description"><?php esc_attr_e('Search news articles for specific keywords or phrases present in the news titles only.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_QUERY_META); ?>"><?php esc_attr_e('Query Meta', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_QUERY_META); ?>" id="<?php echo esc_attr(Arguments::ARG_QUERY_META); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_QUERY_META)); ?>" />
                    <p class="description"><?php esc_attr_e('Search news articles for specific keywords or phrases present in the news titles, URL, meta keywords and meta description only.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_COUNTRY); ?>"><?php esc_attr_e('Country', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_COUNTRY); ?>" id="<?php echo esc_attr(Arguments::ARG_COUNTRY); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_COUNTRY)); ?>" />
                    <p class="description"><?php esc_attr_e('Search the news articles from a specific country. You can add up to 5 countries in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_CATEGORY); ?>"><?php esc_attr_e('Category', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_CATEGORY); ?>" id="<?php echo esc_attr(Arguments::ARG_CATEGORY); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_CATEGORY)); ?>" />
                    <p class="description"><?php esc_attr_e('Search the news articles for a specific category. You can add up to 5 categories in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_EXCLUDE_CATEGORY); ?>"><?php esc_attr_e('Exclude Category', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_EXCLUDE_CATEGORY); ?>" id="<?php echo esc_attr(Arguments::ARG_EXCLUDE_CATEGORY); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_EXCLUDE_CATEGORY)); ?>" />
                    <p class="description"><?php esc_attr_e('You can exclude specific categories to search for news articles. You can exclude up to 5 categories in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_LANGUAGE); ?>"><?php esc_attr_e('Language', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_LANGUAGE); ?>" id="<?php echo esc_attr(Arguments::ARG_LANGUAGE); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_LANGUAGE)); ?>" />
                    <p class="description"><?php esc_attr_e('Search the news articles for a specific language. You can add up to 5 languages in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_DOMAIN); ?>"><?php esc_attr_e('Domain', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_DOMAIN); ?>" id="<?php echo esc_attr(Arguments::ARG_DOMAIN); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_DOMAIN)); ?>" />
                    <p class="description"><?php esc_attr_e('Search the news articles for specific domains or news sources. You can add up to 5 domains in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_EXCLUDE_DOMAIN); ?>"><?php esc_attr_e('Exclude Domain', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_EXCLUDE_DOMAIN); ?>" id="<?php echo esc_attr(Arguments::ARG_EXCLUDE_DOMAIN); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_EXCLUDE_DOMAIN)); ?>" />
                    <p class="description"><?php esc_attr_e('You can exclude specific domains or news sources to search the news articles. You can exclude up to 5 domains in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_DOMAIN_URL); ?>"><?php esc_attr_e('Domain URL', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <input type="text" class="regular-text" name="<?php echo esc_attr(Arguments::ARG_DOMAIN_URL); ?>" id="<?php echo esc_attr(Arguments::ARG_DOMAIN_URL); ?>" value="<?php echo esc_attr($this->settings->get(Arguments::ARG_DOMAIN_URL)); ?>" />
                    <p class="description"><?php esc_attr_e('Search the news articles for specific domains or news sources. You can add up to 5 domains in a single query.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_PRIORITY_DOMAIN); ?>"><?php esc_attr_e('Priority Domain', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <select name="<?php echo esc_attr(Arguments::ARG_PRIORITY_DOMAIN); ?>" id="<?php echo esc_attr(Arguments::ARG_PRIORITY_DOMAIN); ?>">
                        <option><?php esc_attr_e('Select', 'newsdata-io'); ?></option>
                        <option value="top"<?php selected($this->settings->get(Arguments::ARG_PRIORITY_DOMAIN) === 'top'); ?>><?php esc_attr_e('Top', 'newsdata-io'); ?></option>
                        <option value="medium"<?php selected($this->settings->get(Arguments::ARG_PRIORITY_DOMAIN) === 'medium'); ?>><?php esc_attr_e('Medium', 'newsdata-io'); ?></option>
                        <option value="low"<?php selected($this->settings->get(Arguments::ARG_PRIORITY_DOMAIN) === 'low'); ?>><?php esc_attr_e('Low', 'newsdata-io'); ?></option>
                    </select>
                    <p class="description"><?php esc_attr_e('Search the news articles only from top news domains.', 'newsdata-io'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_IMAGE); ?>"><?php esc_attr_e('Image', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <select name="<?php echo esc_attr(Arguments::ARG_IMAGE); ?>" id="<?php echo esc_attr(Arguments::ARG_IMAGE); ?>">
                        <option value=""><?php esc_attr_e('Select', 'newsdata-io'); ?></option>
                        <option value="0"<?php selected($this->settings->get(Arguments::ARG_IMAGE) === 0); ?>><?php esc_attr_e('Without Image', 'newsdata-io'); ?></option>
                        <option value="1"<?php selected($this->settings->get(Arguments::ARG_IMAGE) === 1); ?>><?php esc_attr_e('With Image', 'newsdata-io'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_VIDEO); ?>"><?php esc_attr_e('Video', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <select name="<?php echo esc_attr(Arguments::ARG_VIDEO); ?>" id="<?php echo esc_attr(Arguments::ARG_VIDEO); ?>">
                        <option value=""><?php esc_attr_e('Select', 'newsdata-io'); ?></option>
                        <option value="0"<?php selected($this->settings->get(Arguments::ARG_VIDEO) === 0); ?>><?php esc_attr_e('Without Video', 'newsdata-io'); ?></option>
                        <option value="1"<?php selected($this->settings->get(Arguments::ARG_VIDEO) === 1); ?>><?php esc_attr_e('With Video', 'newsdata-io'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="<?php echo esc_attr(Arguments::ARG_SENTIMENT); ?>"><?php esc_attr_e('Sentiment', 'newsdata-io'); ?></label>
                </th>
                <td>
                    <select name="<?php echo esc_attr(Arguments::ARG_SENTIMENT); ?>" id="<?php echo esc_attr(Arguments::ARG_SENTIMENT); ?>">
                        <option value=""><?php esc_attr_e('Select', 'newsdata-io'); ?></option>
                        <option value="positive"<?php selected($this->settings->get(Arguments::ARG_SENTIMENT) === 'positive'); ?>><?php esc_attr_e('Positive', 'newsdata-io'); ?></option>
                        <option value="negative"<?php selected($this->settings->get(Arguments::ARG_SENTIMENT) === 'negative'); ?>><?php esc_attr_e('Negative', 'newsdata-io'); ?></option>
                        <option value="neutral"<?php selected($this->settings->get(Arguments::ARG_SENTIMENT) === 'neutral'); ?>><?php esc_attr_e('Neutral', 'newsdata-io'); ?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button(__('Save settings', 'newsdata-io'), 'primary', 'newsdata-io-settings'); ?>
    </form>
</div>