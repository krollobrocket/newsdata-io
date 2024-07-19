<?php

namespace Cyclonecode\NewsDataIO\Adapters;

use Cyclonecode\NewsDataIO\Enums\AITags;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Enums\Categories;
use Cyclonecode\NewsDataIO\Enums\Coins;
use Cyclonecode\NewsDataIO\Interfaces\NewsApiInterface;
use Cyclonecode\NewsDataIO\Interfaces\NewsResponseInterface;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;
use GuzzleHttp\ClientInterface;

abstract class AbstractNewsAdapter implements NewsApiInterface
{
    protected Settings $settings;

    protected ClientInterface $client;

    public function __construct(Settings $settings, ClientInterface $client)
    {
        $this->settings = $settings;
        $this->client = $client;
    }

    protected function sanitizeDateArgument(string $key, array $args, string $format = 'Y-m-d'): array
    {
        $date = \DateTimeImmutable::createFromFormat($format, $args[$key]);
        if (!($date && strtolower($date->format($format)) === strtolower($args[$key]))) {
            unset($args[$key]);
        }
        return $args;
    }

    /**
     * @param string $key
     * @param array $args
     * @param array|\Closure $callbackOrAllowedArgs
     * @param int $maxValues
     * @return array
     */
    protected function sanitizeFilterArgument(string $key, array $args, $callbackOrAllowedArgs = null, int $maxValues = 5): array
    {
        $values = array_map('trim', explode(',', $args[$key]));
        if (is_callable($callbackOrAllowedArgs)) {
            $values = array_filter($values, $callbackOrAllowedArgs);
        } elseif (is_array($callbackOrAllowedArgs)) {
            $values = array_filter($values, fn($value) => in_array($value, $callbackOrAllowedArgs, true));
        }
        $values = array_unique($values);
        $args[$key] = implode(',', $maxValues ? array_slice($values, 0, $maxValues) : $values);
        return $args;
    }

    protected function sanitizeArguments(array $args): array
    {
        $args = array_change_key_case($args);
        $args = array_filter($args, fn (string $key) => in_array($key, $this->getAllowedArguments()), ARRAY_FILTER_USE_KEY);

        // Set defaults.
        $defaultArgs = [
            Arguments::ARG_APIKEY => $this->settings->get(Arguments::ARG_APIKEY),
        ];
        foreach (static::getAllowedArguments() as $key) {
            $defaultArgs[$key] = $this->settings->get($key);
        }
        $args = array_merge($defaultArgs, $args);

        // Remove any empty or null arguments.
        $args = array_filter($args, fn ($value) => $value !== null && $value !== '');

        // Sanitize any arguments.
        if (isset($args[Arguments::ARG_SIZE])) {
            if ($this->settings->get(Arguments::ARG_PREMIUM_APIKEY) && $args[Arguments::ARG_SIZE] > Arguments::MAX_SIZE_PREMIUM) {
                $args[Arguments::ARG_SIZE] = Arguments::MAX_SIZE_PREMIUM;
            }
            if (!$this->settings->get(Arguments::ARG_PREMIUM_APIKEY) && $args[Arguments::ARG_SIZE] > Arguments::MAX_SIZE_FREE) {
                $args[Arguments::ARG_SIZE] = Arguments::MAX_SIZE_FREE;
            }
        }
        if (isset($args[Arguments::ARG_LANGUAGE])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_LANGUAGE, $args, $this->getLanguageCodes(), 5);
        }
        if (isset($args[Arguments::ARG_COUNTRY])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_COUNTRY, $args, $this->getCountryCodes(), 5);
        }
        if (isset($args[Arguments::ARG_CATEGORY])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_CATEGORY, $args, $this->getCategoryCodes(), 5);
        }
        if (isset($args[Arguments::ARG_EXCLUDE_CATEGORY])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_EXCLUDE_CATEGORY, $args, $this->getCategoryCodes(), 5);
        }
        if (isset($args[Arguments::ARG_TAG])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_TAG, $args, $this->getAITagCodes(), 5);
        }
        if (isset($args[Arguments::ARG_COIN])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_COIN, $args, $this->getCryptoCoinCodes(), 5);
        }
        if (isset($args[Arguments::ARG_DOMAIN])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_DOMAIN, $args, fn (string $domain) => (bool) filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
        }
        if (isset($args[Arguments::ARG_DOMAIN_URL])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_DOMAIN_URL, $args, fn (string $domain) => (bool) filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
        }
        if (isset($args[Arguments::ARG_EXCLUDE_DOMAIN])) {
            $args = $this->sanitizeFilterArgument(Arguments::ARG_EXCLUDE_DOMAIN, $args, fn (string $domain) => (bool) filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME));
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
        if (isset($args[Arguments::ARG_FROM_DATE])) {
            $args = $this->sanitizeDateArgument(Arguments::ARG_FROM_DATE, $args);
        }
        if (isset($args[Arguments::ARG_TO_DATE])) {
            $args = $this->sanitizeDateArgument(Arguments::ARG_TO_DATE, $args);
        }
        return $args;
    }

    public function getAllowedArguments(): array
    {
        return [
            Arguments::ARG_ID,
            Arguments::ARG_SIZE,
            Arguments::ARG_QUERY,
            Arguments::ARG_QUERY_TITLE,
            Arguments::ARG_QUERY_META,
            Arguments::ARG_LANGUAGE,
            Arguments::ARG_DOMAIN,
            Arguments::ARG_DOMAIN_URL,
            Arguments::ARG_EXCLUDE_DOMAIN,
            Arguments::ARG_PRIORITY_DOMAIN,
            Arguments::ARG_VIDEO,
            Arguments::ARG_IMAGE,
            Arguments::ARG_TIMEZONE,
            Arguments::ARG_TIMEFRAME,
            Arguments::ARG_REMOVE_DUPLICATE,
            // @todo: For now we remove the premium arguments.
            // Arguments::ARG_TAG,
            // Arguments::ARG_SENTIMENT,
            // Arguments::ARG_REGION,
            // Arguments::ARG_FULL_CONTENT,
        ];
    }

    public function getAITags(): array
    {
        return [
            AITags::TAG_ACCIDENTS => __('Accidents', 'newsdata-io'),
            AITags::TAG_AGRICULTURE_AND_FARMING => __('Agriculture and Farming', 'newsdata-io'),
            AITags::TAG_AMERICAN_FOOTBALL => __('American Football', 'newsdata-io'),
            AITags::TAG_ART_AND_CULTURE => __('Art and Culture', 'newsdata-io'),
        ];
    }

    public function getAITagCodes(): array
    {
        return array_keys($this->getAITags());
    }

    public function getCryptoCoins(): array
    {
        return [
            Coins::COIN_BINANCE => __('Binance', 'newsdata-io'),
            Coins::COIN_BITCOIN => __('Bitcoin', 'newsdata-io'),
            Coins::COIN_ETHEREUM => __('Ethereum', 'newsdata-io'),
            Coins::COIN_TETHER => __('Tether', 'newsdata-io'),
        ];
    }

    public function getCryptoCoinCodes(): array
    {
        return array_keys($this->getCryptoCoins());
    }

    public function getCountries(): array
    {
        return [
            'af' => __('Afghanistan', 'newsdata-io'),
            'al' => __('Albania', 'newsdata-io'),
            'dz' => __('Algeria', 'newsdata-io'),
            'ad' => __('Andorra', 'newsdata-io'),
            'ao' => __('Angola', 'newsdata-io'),
            'ar' => __('Argentina', 'newsdata-io'),
            'am' => __('Armenia', 'newsdata-io'),
            'au' => __('Australia', 'newsdata-io'),
            'at' => __('Austria', 'newsdata-io'),
            'az' => __('Azerbaijan', 'newsdata-io'),
            'bs' => __('Bahamas', 'newsdata-io'),
            'bh' => __('Bahrain', 'newsdata-io'),
            'bd' => __('Bangladesh', 'newsdata-io'),
            'bb' => __('Barbados', 'newsdata-io'),
            'by' => __('Belarus', 'newsdata-io'),
            'be' => __('Belgium', 'newsdata-io'),
            'bz' => __('Belize', 'newsdata-io'),
            'bj' => __('Benin', 'newsdata-io'),
            'bm' => __('Bermuda', 'newsdata-io'),
            'bt' => __('Bhutan', 'newsdata-io'),
            'bo' => __('Bolivia', 'newsdata-io'),
            'ba' => __('Bosnia and Herzegovina', 'newsdata-io'),
            'bw' => __('Botswana', 'newsdata-io'),
            'br' => __('Brazil', 'newsdata-io'),
            'bn' => __('Brunei', 'newsdata-io'),
            'bg' => __('Bulgaria', 'newsdata-io'),
            'bf' => __('Burkina Fasco', 'newsdata-io'),
            'bi' => __('Burundi', 'newsdata-io'),
            'kh' => __('Cambodia', 'newsdata-io'),
            'cm' => __('Cameroon', 'newsdata-io'),
            'ca' => __('Canada', 'newsdata-io'),
            'cv' => __('Cape Verde', 'newsdata-io'),
            'ky' => __('Cayman Islands', 'newsdata-io'),
            'cf' => __('Central African Republic', 'newsdata-io'),
            'td' => __('Chad', 'newsdata-io'),
            'cl' => __('Chile', 'newsdata-io'),
            'cn' => __('China', 'newsdata-io'),
            'co' => __('Colombia', 'newsdata-io'),
            'km' => __('Comoros', 'newsdata-io'),
            'cg' => __('Congo', 'newsdata-io'),
            'ck' => __('Cook Islands', 'newsdata-io'),
            'cr' => __('Costa Rica', 'newsdata-io'),
            'hr' => __('Croatia', 'newsdata-io'),
            'cu' => __('Cuba', 'newsdata-io'),
            'cy' => __('Cyprus', 'newsdata-io'),
            'cz' => __('Czech Republic', 'newsdata-io'),
            'dk' => __('Denmark', 'newsdata-io'),
            'dj' => __('Djibouti', 'newsdata-io'),
            'dm' => __('Dominica', 'newsdata-io'),
            'do' => __('Dominican Republic', 'newsdata-io'),
            'cd' => __('DR Congo', 'newsdata-io'),
            'ec' => __('Ecuador', 'newsdata-io'),
            'eg' => __('Egypt', 'newsdata-io'),
            'sv' => __('El Salvador', 'newsdata-io'),
            'gq' => __('Equatorial Guinea', 'newsdata-io'),
            'er' => __('Eritrea', 'newsdata-io'),
            'ee' => __('Estonia', 'newsdata-io'),
            'sz' => __('Eswatini', 'newsdata-io'),
            'et' => __('Ethiopia', 'newsdata-io'),
            'fj' => __('Fiji', 'newsdata-io'),
            'fi' => __('Finland', 'newsdata-io'),
            'fr' => __('France', 'newsdata-io'),
            'pf' => __('French Polynesia', 'newsdata-io'),
            'ga' => __('Gabon', 'newsdata-io'),
            'gm' => __('Gambia', 'newsdata-io'),
            'ge' => __('Georgia', 'newsdata-io'),
            'de' => __('Germany', 'newsdata-io'),
            'gh' => __('Ghana', 'newsdata-io'),
            'gi' => __('Gibraltar', 'newsdata-io'),
            'gr' => __('Greece', 'newsdata-io'),
            'gd' => __('Grenada', 'newsdata-io'),
            'gt' => __('Guatemala', 'newsdata-io'),
            'gn' => __('Guinea', 'newsdata-io'),
            'gy' => __('Guyana', 'newsdata-io'),
            'ht' => __('Haiti', 'newsdata-io'),
            'hn' => __('Honduras', 'newsdata-io'),
            'hk' => __('Hong Kong', 'newsdata-io'),
            'hu' => __('Hungary', 'newsdata-io'),
            'is' => __('Iceland', 'newsdata-io'),
            'in' => __('India', 'newsdata-io'),
            'id' => __('Indonesia', 'newsdata-io'),
            'ir' => __('Iran', 'newsdata-io'),
            'iq' => __('Iraq', 'newsdata-io'),
            'ie' => __('Ireland', 'newsdata-io'),
            'il' => __('Israel', 'newsdata-io'),
            'it' => __('Italy', 'newsdata-io'),
            'ci' => __('Ivory Coast', 'newsdata-io'),
            'jm' => __('Jamaica', 'newsdata-io'),
            'jp' => __('Japan', 'newsdata-io'),
            'je' => __('Jersey', 'newsdata-io'),
            'jo' => __('Jordan', 'newsdata-io'),
            'kz' => __('Kazakhstan', 'newsdata-io'),
            'ke' => __('Kenya', 'newsdata-io'),
            'ki' => __('Kiribati', 'newsdata-io'),
            'xk' => __('Kosovo', 'newsdata-io'),
            'kw' => __('Kuwait', 'newsdata-io'),
            'kg' => __('Kyrgyzstan', 'newsdata-io'),
            'la' => __('Laos', 'newsdata-io'),
            'lv' => __('Latvia', 'newsdata-io'),
            'lb' => __('Lebanon', 'newsdata-io'),
            'ls' => __('Lesotho', 'newsdata-io'),
            'lr' => __('Liberia', 'newsdata-io'),
            'ly' => __('Libya', 'newsdata-io'),
            'li' => __('Liechtenstein', 'newsdata-io'),
            'lt' => __('Lithuania', 'newsdata-io'),
            'lu' => __('Luxembourg', 'newsdata-io'),
            'mo' => __('Macao', 'newsdata-io'),
            'mk' => __('Macedonia', 'newsdata-io'),
            'mg' => __('Madagascar', 'newsdata-io'),
            'mw' => __('Malawi', 'newsdata-io'),
            'my' => __('Malaysia', 'newsdata-io'),
            'mv' => __('Maldives', 'newsdata-io'),
            'ml' => __('Mali', 'newsdata-io'),
            'mt' => __('Malta', 'newsdata-io'),
            'mh' => __('Marshall Islands', 'newsdata-io'),
            'mr' => __('Mauritania', 'newsdata-io'),
            'mu' => __('Mauritius', 'newsdata-io'),
            'mx' => __('Mexico', 'newsdata-io'),
            'fm' => __('Micronesia', 'newsdata-io'),
            'md' => __('Moldova', 'newsdata-io'),
            'mc' => __('Monaco', 'newsdata-io'),
            'mn' => __('Mongolia', 'newsdata-io'),
            'me' => __('Montenegro', 'newsdata-io'),
            'ma' => __('Morocco', 'newsdata-io'),
            'mz' => __('Mozambique', 'newsdata-io'),
            'mm' => __('Myanmar', 'newsdata-io'),
            'na' => __('Namibia', 'newsdata-io'),
            'nr' => __('Nauru', 'newsdata-io'),
            'np' => __('Nepal', 'newsdata-io'),
            'nl' => __('Netherlands', 'newsdata-io'),
            'nc' => __('New Caledonia', 'newsdata-io'),
            'nz' => __('New Zealand', 'newsdata-io'),
            'ni' => __('Nicaragua', 'newsdata-io'),
            'ne' => __('Niger', 'newsdata-io'),
            'ng' => __('Nigeria', 'newsdata-io'),
            'kp' => __('North Korea', 'newsdata-io'),
            'no' => __('Norway', 'newsdata-io'),
            'om' => __('Oman', 'newsdata-io'),
            'pk' => __('Pakistan', 'newsdata-io'),
            'pw' => __('Palau', 'newsdata-io'),
            'ps' => __('Palestine', 'newsdata-io'),
            'pa' => __('Panama', 'newsdata-io'),
            'pg' => __('Papua New Guinea', 'newsdata-io'),
            'py' => __('Paraguay', 'newsdata-io'),
            'pe' => __('Peru', 'newsdata-io'),
            'ph' => __('Philippines', 'newsdata-io'),
            'pl' => __('Poland', 'newsdata-io'),
            'pt' => __('Portugal', 'newsdata-io'),
            'pr' => __('Puerto Rico', 'newsdata-io'),
            'qa' => __('Qatar', 'newsdata-io'),
            'ro' => __('Romania', 'newsdata-io'),
            'ru' => __('Russia', 'newsdata-io'),
            'rw' => __('Rwanda', 'newsdata-io'),
            'lc' => __('Saint Lucia', 'newsdata-io'),
            'sx' => __('Saint Martin (dutch)', 'newsdata-io'),
            'ws' => __('Samoa', 'newsdata-io'),
            'sm' => __('San Marino', 'newsdata-io'),
            'st' => __('Sao Tome and Principe', 'newsdata-io'),
            'sa' => __('Saudi Arabia', 'newsdata-io'),
            'sn' => __('Senegal', 'newsdata-io'),
            'rs' => __('Serbia', 'newsdata-io'),
            'sc' => __('Seychelles', 'newsdata-io'),
            'sl' => __('Sierra Leone', 'newsdata-io'),
            'sg' => __('Singapore', 'newsdata-io'),
            'sk' => __('Slovakia', 'newsdata-io'),
            'si' => __('Slovenia', 'newsdata-io'),
            'sb' => __('Solomon Islands', 'newsdata-io'),
            'so' => __('Somalia', 'newsdata-io'),
            'za' => __('South Africa', 'newsdata-io'),
            'kr' => __('South Korea', 'newsdata-io'),
            'es' => __('Spain', 'newsdata-io'),
            'lk' => __('Sri Lanka', 'newsdata-io'),
            'sd' => __('Sudan', 'newsdata-io'),
            'sr' => __('Suriname', 'newsdata-io'),
            'se' => __('Sweden', 'newsdata-io'),
            'ch' => __('Switzerland', 'newsdata-io'),
            'sy' => __('Syria', 'newsdata-io'),
            'tw' => __('Taiwan', 'newsdata-io'),
            'tj' => __('Tajikistan', 'newsdata-io'),
            'tz' => __('Tanzania', 'newsdata-io'),
            'th' => __('Thailand', 'newsdata-io'),
            'tl' => __('Timor-Leste', 'newsdata-io'),
            'tg' => __('Togo', 'newsdata-io'),
            'to' => __('Tonga', 'newsdata-io'),
            'tt' => __('Trinidad and Tobago', 'newsdata-io'),
            'tn' => __('Tunisia', 'newsdata-io'),
            'tr' => __('Turkey', 'newsdata-io'),
            'tm' => __('Turkmenistan', 'newsdata-io'),
            'tv' => __('Tuvalu', 'newsdata-io'),
            'ug' => __('Uganda', 'newsdata-io'),
            'ua' => __('Ukraine', 'newsdata-io'),
            'ae' => __('United Arab Emirates', 'newsdata-io'),
            'gb' => __('United Kingdom', 'newsdata-io'),
            'us' => __('United States of America', 'newsdata-io'),
            'uy' => __('Uruguay', 'newsdata-io'),
            'uz' => __('Uzbekistan', 'newsdata-io'),
            'vu' => __('Vanuatu', 'newsdata-io'),
            'va' => __('Vatican', 'newsdata-io'),
            've' => __('Venezuela', 'newsdata-io'),
            'vn' => __('Vietnam', 'newsdata-io'),
            'vg' => __('Virgin Islands (British)', 'newsdata-io'),
            'wo' => __('World', 'newsdata-io'),
            'ye' => __('Yemen', 'newsdata-io'),
            'zm' => __('Zambia', 'newsdata-io'),
            'zw' => __('Zimbabwe', 'newsdata-io'),
        ];
    }

    /**
     * @return string[]
     */
    public function getCountryCodes(): array
    {
        return array_keys($this->getCountries());
    }

    public function getLanguages(): array
    {
        return [
            'af' => __('Afrikaans', 'newsdata-io'),
            'sq' => __('Albanian', 'newsdata-io'),
            'am' => __('Amharic', 'newsdata-io'),
            'ar' => __('Arabic', 'newsdata-io'),
            'hy' => __('Armenian', 'newsdata-io'),
            'as' => __('Assamese', 'newsdata-io'),
            'az' => __('Azerbaijani', 'newsdata-io'),
            'bm' => __('Bambara', 'newsdata-io'),
            'eu' => __('Basque', 'newsdata-io'),
            'be' => __('Belarusian', 'newsdata-io'),
            'bn' => __('Bengali', 'newsdata-io'),
            'bs' => __('Bosnian', 'newsdata-io'),
            'bg' => __('Bulgarian', 'newsdata-io'),
            'my' => __('Burmese', 'newsdata-io'),
            'ca' => __('Catalan', 'newsdata-io'),
            'ckb' => __('Central Kurdish', 'newsdata-io'),
            'zh' => __('Chinese', 'newsdata-io'),
            'hr' => __('Croatian', 'newsdata-io'),
            'cs' => __('Czech', 'newsdata-io'),
            'da' => __('Danish', 'newsdata-io'),
            'nl' => __('Dutch', 'newsdata-io'),
            'en' => __('English', 'newsdata-io'),
            'et' => __('Estonian', 'newsdata-io'),
            'pi' => __('Filipino', 'newsdata-io'),
            'fi' => __('Finnish', 'newsdata-io'),
            'fr' => __('French', 'newsdata-io'),
            'gl' => __('Galician', 'newsdata-io'),
            'ka' => __('Georgian', 'newsdata-io'),
            'de' => __('German', 'newsdata-io'),
            'el' => __('Greek', 'newsdata-io'),
            'gu' => __('Gujarati', 'newsdata-io'),
            'ha' => __('Hausa', 'newsdata-io'),
            'he' => __('Hebrew', 'newsdata-io'),
            'hi' => __('Hindi', 'newsdata-io'),
            'hu' => __('Hungarian', 'newsdata-io'),
            'is' => __('Icelandic', 'newsdata-io'),
            'id' => __('Indonesian', 'newsdata-io'),
            'it' => __('Italian', 'newsdata-io'),
            'ja' => __('Japanese', 'newsdata-io'),
            'kn' => __('Kannada', 'newsdata-io'),
            'kz' => __('Kazakh', 'newsdata-io'),
            'kh' => __('Khmer', 'newsdata-io'),
            'rw' => __('Kinyarwanda', 'newsdata-io'),
            'ko' => __('Korean', 'newsdata-io'),
            'ku' => __('Kurdish', 'newsdata-io'),
            'lv' => __('Latvian', 'newsdata-io'),
            'lt' => __('Lithuanian', 'newsdata-io'),
            'lb' => __('Luxembourgish', 'newsdata-io'),
            'mk' => __('Macedonian', 'newsdata-io'),
            'ms' => __('Malay', 'newsdata-io'),
            'ml' => __('Malayalam', 'newsdata-io'),
            'mt' => __('Maltese', 'newsdata-io'),
            'mi' => __('Maori', 'newsdata-io'),
            'mr' => __('Marathi', 'newsdata-io'),
            'mn' => __('Mongolian', 'newsdata-io'),
            'ne' => __('Nepali', 'newsdata-io'),
            'no' => __('Norwegian', 'newsdata-io'),
            'or' => __('Oriya', 'newsdata-io'),
            'ps' => __('Pashto', 'newsdata-io'),
            'fa' => __('Persian', 'newsdata-io'),
            'pl' => __('Polish', 'newsdata-io'),
            'pt' => __('Portuguese', 'newsdata-io'),
            'pa' => __('Punjabi', 'newsdata-io'),
            'ro' => __('Romanian', 'newsdata-io'),
            'ru' => __('Russian', 'newsdata-io'),
            'sm' => __('Samoan', 'newsdata-io'),
            'sr' => __('Serbian', 'newsdata-io'),
            'sn' => __('Shona', 'newsdata-io'),
            'sd' => __('Sindhi', 'newsdata-io'),
            'si' => __('Sinhala', 'newsdata-io'),
            'sk' => __('Slovak', 'newsdata-io'),
            'sl' => __('Slovenian', 'newsdata-io'),
            'so' => __('Somali', 'newsdata-io'),
            'es' => __('Spanish', 'newsdata-io'),
            'sw' => __('Swahili', 'newsdata-io'),
            'sv' => __('Swedish', 'newsdata-io'),
            'tg' => __('Tajik', 'newsdata-io'),
            'ta' => __('Tamil', 'newsdata-io'),
            'te' => __('Telugu', 'newsdata-io'),
            'th' => __('Thai', 'newsdata-io'),
            'zht' => __('Traditional chinese', 'newsdata-io'),
            'tr' => __('Turkish', 'newsdata-io'),
            'tk' => __('Turkmen', 'newsdata-io'),
            'uk' => __('Ukrainian', 'newsdata-io'),
            'ur' => __('Urdu', 'newsdata-io'),
            'uz' => __('Uzbek', 'newsdata-io'),
            'vi' => __('Vietnamese', 'newsdata-io'),
            'cy' => __('Welsh', 'newsdata-io'),
            'zu' => __('Zulu', 'newsdata-io'),
        ];
    }

    /**
     * @return string[]
     */
    public function getLanguageCodes(): array
    {
        return array_keys($this->getLanguages());
    }

    public function getCategories(): array
    {
        return [
            Categories::CATEGORY_BUSINESS => __('Business', 'newsdata-io'),
            Categories::CATEGORY_CRIME => __('Crime', 'newsdata-io'),
            Categories::CATEGORY_DOMESTIC => __('Domestic', 'newsdata-io'),
            Categories::CATEGORY_EDUCATION => __('Education', 'newsdata-io'),
            Categories::CATEGORY_ENTERTAINMENT => __('Entertainment', 'newsdata-io'),
            Categories::CATEGORY_ENVIRONMENT => __('Environment', 'newsdata-io'),
            Categories::CATEGORY_FOOD => __('Food', 'newsdata-io'),
            Categories::CATEGORY_HEALTH => __('Health', 'newsdata-io'),
            Categories::CATEGORY_LIFESTYLE => __('Lifestyle', 'newsdata-io'),
            Categories::CATEGORY_OTHER => __('Other', 'newsdata-io'),
            Categories::CATEGORY_POLITICS => __('Politics', 'newsdata-io'),
            Categories::CATEGORY_SCIENCE => __('Science', 'newsdata-io'),
            Categories::CATEGORY_SPORTS => __('Sports', 'newsdata-io'),
            Categories::CATEGORY_TECHNOLOGY => __('Technology', 'newsdata-io'),
            Categories::CATEGORY_TOP => __('Top', 'newsdata-io'),
            Categories::CATEGORY_TOURISM => __('Tourism', 'newsdata-io'),
            Categories::CATEGORY_WORLD => __('World', 'newsdata-io'),
        ];
    }

    public function getCategoryCodes(): array
    {
        return array_keys($this->getCategories());
    }

    abstract public function getNews(array $args = []): ?NewsResponseInterface;
}
