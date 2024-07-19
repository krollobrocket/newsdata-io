<?php

namespace Cyclonecode\NewsDataIO\Adapters;

use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Interfaces\NewsResponseInterface;

class NewsArchiveAdapter extends AbstractNewsAdapter
{
    const API_ENDPOINT = 'https://newsdata.io/api/1/archive';

    public function getAllowedArguments(): array
    {
        return array_merge(parent::getAllowedArguments(), [
            Arguments::ARG_COUNTRY,
            Arguments::ARG_CATEGORY,
            Arguments::ARG_EXCLUDE_CATEGORY,
            Arguments::ARG_FROM_DATE,
            Arguments::ARG_TO_DATE,
        ]);
    }

    public function getNews(array $args = []): ?NewsResponseInterface
    {
        // @todo: fetch archived news.
        $args = $this->sanitizeArguments($args);
        return null;
    }
}
