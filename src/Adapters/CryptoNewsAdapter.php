<?php

namespace Cyclonecode\NewsDataIO\Adapters;

use Cyclonecode\NewsDataIO\Enums\Coins;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Interfaces\NewsResponseInterface;

class CryptoNewsAdapter extends AbstractNewsAdapter
{
    const API_ENDPOINT = 'https://newsdata.io/api/1/crypto';

    public function getNews(array $args = []): ?NewsResponseInterface
    {
        // @todo: fetch crypto news.
        $args = $this->sanitizeArguments($args);
        return null;
    }

    public function getAllowedArguments(): array
    {
        return array_merge(parent::getAllowedArguments(), [
            Arguments::ARG_COIN,
        ]);
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
}
