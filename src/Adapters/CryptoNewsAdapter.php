<?php

namespace Cyclonecode\NewsDataIO\Adapters;

use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Exceptions\NewsApiException;
use Cyclonecode\NewsDataIO\Interfaces\NewsResponseInterface;
use Cyclonecode\NewsDataIO\Responses\NewsResponse;

class CryptoNewsAdapter extends AbstractNewsAdapter
{
    const API_ENDPOINT = 'https://newsdata.io/api/1/crypto';

    public function getNews(array $args = []): ?NewsResponseInterface
    {
        try {
            $args = $this->sanitizeArguments($args);
            $response = \json_decode($this->client->request('GET', self::API_ENDPOINT, [
                'query' => $args,
            ])->getBody()->getContents());
            // @phpstan-ignore-next-line
            if ($response->status !== 'success') {
                // @phpstan-ignore-next-line
                throw new NewsApiException($response->results->message);
            }

            // @phpstan-ignore-next-line
            return new NewsResponse($response);
        } catch (\Exception $e) {
            // @todo: log exception.
        }

        return null;
    }

    public function getAllowedArguments(): array
    {
        return array_merge(parent::getAllowedArguments(), [
            Arguments::ARG_COIN,
            Arguments::ARG_TAG,
            Arguments::ARG_SENTIMENT,
        ]);
    }
}
