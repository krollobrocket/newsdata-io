<?php

namespace Cyclonecode\NewsDataIO\Adapters;

use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Exceptions\NewsApiException;
use Cyclonecode\NewsDataIO\Interfaces\NewsResponseInterface;
use Cyclonecode\NewsDataIO\Responses\NewsResponse;

class LatestNewsAdapter extends AbstractNewsAdapter
{
    const API_ENDPOINT = 'https://newsdata.io/api/1/latest';

    public function getAllowedArguments(): array
    {
        return array_merge(parent::getAllowedArguments(), [
            Arguments::ARG_COUNTRY,
            Arguments::ARG_CATEGORY,
            Arguments::ARG_EXCLUDE_CATEGORY,
        ]);
    }

    public function getNews(array $args = []): ?NewsResponseInterface
    {
        try {
            $args = $this->sanitizeArguments($args);
            if (isset($args[Arguments::ARG_SIZE])) {
                if ($this->settings->get(Arguments::ARG_PREMIUM_APIKEY) && $args[Arguments::ARG_SIZE] > Arguments::MAX_SIZE_PREMIUM) {
                    $args[Arguments::ARG_SIZE] = Arguments::MAX_SIZE_PREMIUM;
                }
                if (!$this->settings->get(Arguments::ARG_PREMIUM_APIKEY) && $args[Arguments::ARG_SIZE] > Arguments::MAX_SIZE_FREE) {
                    $args[Arguments::ARG_SIZE] = Arguments::MAX_SIZE_FREE;
                }
            }
            if (isset($args[Arguments::ARG_LANGUAGE])) {
                // @todo: We could use the helper method sanitizeArray here.
                $allLanguageCodes = $this->getLanguageCodes();
                $languageCodes = array_map('trim', explode(',', $args[Arguments::ARG_LANGUAGE]));
                $languageCodes = array_filter($languageCodes, fn ($languageCode) => in_array($languageCode, $allLanguageCodes, true));
                $languageCodes = array_unique($languageCodes);
                $args[Arguments::ARG_LANGUAGE] = implode(',', array_slice($languageCodes, 0, 5));
            }
            if (isset($args[Arguments::ARG_COUNTRY])) {
                $allCountryCodes = $this->getCountryCodes();
                $countryCodes = array_map('trim', explode(',', $args[Arguments::ARG_COUNTRY]));
                $countryCodes = array_filter($countryCodes, fn ($countryCode) => in_array($countryCode, $allCountryCodes, true));
                $countryCodes = array_unique($countryCodes);
                $args[Arguments::ARG_COUNTRY] = implode(',', array_slice($countryCodes, 0, 5));
            }
            if (isset($args[Arguments::ARG_CATEGORY])) {
                $allCategoryCodes = $this->getCategoryCodes();
                $categoryCodes = array_map('trim', explode(',', $args[Arguments::ARG_CATEGORY]));
                $categoryCodes = array_filter($categoryCodes, fn ($categoryCode) => in_array($categoryCode, $allCategoryCodes, true));
                $categoryCodes = array_unique($categoryCodes);
                $args[Arguments::ARG_CATEGORY] = implode(',', array_slice($categoryCodes, 0, 5));
            }
            if (isset($args[Arguments::ARG_EXCLUDE_CATEGORY])) {
                $allCategoryCodes = $this->getCategoryCodes();
                $categoryCodes = array_map('trim', explode(',', $args[Arguments::ARG_EXCLUDE_CATEGORY]));
                $categoryCodes = array_filter($categoryCodes, fn ($categoryCode) => in_array($categoryCode, $allCategoryCodes, true));
                $categoryCodes = array_unique($categoryCodes);
                $args[Arguments::ARG_EXCLUDE_CATEGORY] = implode(',', array_slice($categoryCodes, 0, 5));
            }
            if (isset($args[Arguments::ARG_DOMAIN])) {
                $domains = array_map('trim', explode(',', $args[Arguments::ARG_DOMAIN]));
                $domains = array_filter($domains, fn (string $domain) => filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
                $domains = array_unique($domains);
                $args[Arguments::ARG_DOMAIN] = implode(',', array_slice($domains, 0, 5));
            }
            if (isset($args[Arguments::ARG_DOMAIN_URL])) {
                $domains = array_map('trim', explode(',', $args[Arguments::ARG_DOMAIN_URL]));
                $domains = array_filter($domains, fn (string $domain) => filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
                $domains = array_unique($domains);
                $args[Arguments::ARG_DOMAIN_URL] = implode(',', array_slice($domains, 0, 5));
            }
            if (isset($args[Arguments::ARG_EXCLUDE_DOMAIN])) {
                $domains = array_map('trim', explode(',', $args[Arguments::ARG_EXCLUDE_DOMAIN]));
                $domains = array_filter($domains, fn (string $domain) => filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
                $domains = array_unique($domains);
                $args[Arguments::ARG_EXCLUDE_DOMAIN] = implode(',', array_slice($domains, 0, 5));
            }
            if (isset($args[Arguments::ARG_TAG])) {
                $tags = array_map('trim', explode(',', $args[Arguments::ARG_TAG]));
                $tags = array_filter($tags);
                $tags = array_unique($tags);
                $args[Arguments::ARG_TAG] = implode(',', array_slice($tags, 0, 5));
            }
            if (isset($args[Arguments::ARG_DOMAIN], $args[Arguments::ARG_DOMAIN_URL])) {
                // We cannot use both.
                unset($args[Arguments::ARG_DOMAIN_URL]);
            }
            if (isset($args[Arguments::ARG_DOMAIN], $args[Arguments::ARG_EXCLUDE_DOMAIN]) || isset($args[Arguments::ARG_DOMAIN_URL], $args[Arguments::ARG_EXCLUDE_DOMAIN])) {
                // We cannot use both.
                unset($args[Arguments::ARG_EXCLUDE_DOMAIN]);
            }
            if (isset($args[Arguments::ARG_CATEGORY], $args[Arguments::ARG_EXCLUDE_CATEGORY])) {
                // We cannot use both.
                unset($args[Arguments::ARG_EXCLUDE_CATEGORY]);
            }
            if (isset($args[Arguments::ARG_QUERY], $args[Arguments::ARG_QUERY_TITLE])) {
                // We cannot use both.
                unset($args[Arguments::ARG_QUERY_TITLE]);
            }
            if (isset($args[Arguments::ARG_QUERY], $args[Arguments::ARG_QUERY_META])) {
                // We cannot use both.
                unset($args[Arguments::ARG_QUERY_META]);
            }
            if (isset($args[Arguments::ARG_QUERY_TITLE], $args[Arguments::ARG_QUERY_META])) {
                // We cannot use both.
                unset($args[Arguments::ARG_QUERY_META]);
            }
            $response = \json_decode($this->client->request('GET', self::API_ENDPOINT, [
                'query' => $args,
            ])->getBody()->getContents());
            if ($response->status !== 'success') {
                throw new NewsApiException($response->results->message);
            }
            return new NewsResponse($response);
        } catch (\Exception $e) {
            // @todo: log exception.
        }
        return null;
    }
}
