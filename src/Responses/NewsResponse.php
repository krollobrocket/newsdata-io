<?php

namespace Cyclonecode\NewsDataIO\Responses;

use Cyclonecode\NewsDataIO\Interfaces\NewsResponseInterface;

class NewsResponse implements NewsResponseInterface
{
    protected string $status;
    protected int $totalResults;
    /** @var Article[] */
    protected array $results = [];
    protected ?string $nextPage;

    public function __construct(\stdClass $data)
    {
        $this->status = $data->status;
        $this->totalResults = $data->totalResults;
        $this->results = array_map(function (\stdClass $article) {
            return new Article($article);
        }, $data->results ?? []);
        $this->nextPage = $data->nextPage;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @return Article[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    public function getNextPage(): ?string
    {
        return $this->nextPage;
    }
}
