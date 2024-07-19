# News Data IO

[![Run Tests](https://github.com/krollobrocket/newsdata-io/actions/workflows/run_tests.yml/badge.svg?branch=master)](https://github.com/krollobrocket/newsdata-io/actions/workflows/run_tests.yml)

**Contributors:** cyclonecode \
**Donate link:** https://www.buymeacoffee.com/cyclonecode \
**Tags:** news, article, feed \
**Requires at least:** 4.0.0 \
**Tested up to:** 6.6.1 \
**Requires PHP:** 7.4 \
**Stable tag:** 1.0.0 \
**License:** GPLv2 or later \
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

This plugin display a news feed fetched from newsdata.io.

## Description

This plugin is developed by [Cyclonecode](https://profiles.wordpress.org/cyclonecode) and can be used to display a news feed fetched from newsdata.io.

First you need to get an API key from newsdata.io by following the steps as described here: [How to get the NewsData.io API key](https://newsdata.io/documentation/#get-newdata-api-keyhttps://newsdata.io/documentation/#get-newdata-api-key).

If you have questions or perhaps some idea on things that should be added you can also try [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

### Shortcode

The shortcode **[newsdata-io]** can either be used by adding it to the content field of any post or page or by using the **do_shortcode** function in one of your templates.

Shortcode attributes:

- size

Number of news. This value cannot be larger than 10 when using a free api key, otherwise the maximum is 50.

- q

Search news articles for specific keywords or phrases present in the news title, content, URL, meta keywords and meta description.
This attribute cannot be combined with the qInMeta or qInTitle attributes.

- qInTitle

Search news articles for specific keywords or phrases present in the news titles only.
This attribute cannot be combined with the q or qInMeta attributes.

- qInMeta

Search news articles for specific keywords or phrases present in the news titles, URL, meta keywords and meta description only.
This attribute cannot be combined with the q or qInTitle attributes.

- country

Search the news articles from a specific country. You can add up to 5 countries in a single query.

- category

Search the news articles for a specific category. You can add up to 5 categories in a single query.

- excludeCategory

You can exclude specific categories to search for news articles. You can exclude up to 5 categories in a single query.

- language

Search the news articles for a specific language. You can add up to 5 languages in a single query.

- domain

Search the news articles for specific domains or news sources. You can add up to 5 domains in a single query.

- excludeDomain

You can exclude specific domains or news sources to search the news articles. You can exclude up to 5 domains in a single query.

- domainUrl

Search the news articles for specific domains or news sources. You can add up to 5 domains in a single query.

- priorityDomain

Search the news articles only from top news domains. Valid values are top, medium and low.

- image

Only fetch news articles with or without an image.

- video

Only fetch news articles with or without a video.

Here is an example using all of the above attributes (notice that some of the attributes cannot be combined; this is just an example):

`[newsdata-io size=5 q="Donald" qInTitle="Trump" qInMeta="Donald duck" country="us,gb,dk" category="sports,technology" excludeCategory="crime,domestic" language="sv,en" domain="bbc" excludeDomain="example" domainUrl="bbc.com" priorityDomain="top" image=1 video=0]`

**Notice** that all shortcode attributes are optional and that they **must** be on a single line.
Default values is taken from the plugins settings page.

## Improvements

If you have any ideas for improvements, don't hesitate to email me at cyclonecode@gmail.com or send me a message on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

## Support

If you run into any trouble, don’t hesitate to add a new topic under the support section:
[https://wordpress.org/support/plugin/newsdata-io](https://wordpress.org/support/plugin/newsdata-io)

You can also try contacting me on [slack](https://join.slack.com/t/cyclonecode/shared_invite/zt-6bdtbdab-n9QaMLM~exHP19zFDPN~AQ).

## Installation

1. Upload newsdata-io to the **/wp-content/plugins/** directory,
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Add your Api Key and configure the plugin at **/wp-admin/admin.php?page=newsdata-io** in WordPress.
4. You can then add a shortcode in order to display the news listing.

## Frequently Asked Questions

## Upgrade Notice

## Screenshots

### 1. A basic news listing.

[missing image]


## Changelog

