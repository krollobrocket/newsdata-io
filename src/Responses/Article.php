<?php

namespace Cyclonecode\NewsDataIO\Responses;

use Cyclonecode\NewsDataIO\Interfaces\ArticleInterface;

class Article implements ArticleInterface
{
    protected string $article_id;

    protected string $title;

    protected string $link;

    protected array $keywords = [];

    protected array $creator = [];

    protected ?string $video_url;

    protected ?string $description;

    protected string $content;

    protected \DateTimeImmutable $pubDate;

    protected ?string $image_url;

    protected string $source_id;

    protected int $source_priority;

    protected string $source_url;

    protected ?string $source_icon;

    protected string $language;

    protected array $country = [];

    protected array $category = [];

    protected bool $duplicate;

    // professional and corporate plans
    protected string $ai_tag;

    protected string $sentiment;

    protected string $sentiment_stats;

    protected string $ai_region;

    protected string $ai_org;

    public function __construct(\stdClass $data)
    {
        $this->article_id = $data->article_id;
        $this->title = $data->title;
        $this->link = $data->link;
        $this->keywords = $data->keywords ?? [];
        $this->creator = $data->creator ?? [];
        $this->video_url = $data->video_url;
        $this->description = $data->description;
        $this->content = $data->content;
        $this->pubDate = new \DateTimeImmutable($data->pubDate);
        $this->image_url = $data->image_url;
        $this->source_id = $data->source_id;
        $this->source_priority = $data->source_priority;
        $this->source_url = $data->source_url;
        $this->source_icon = $data->source_icon;
        $this->language = $data->language;
        $this->country = $data->country;
        $this->category = $data->category;
        // next is premium fields.
        $this->ai_tag = $data->ai_tag;
        $this->sentiment = $data->sentiment;
        $this->sentiment_stats = $data->sentiment_stats;
        $this->ai_org = $data->ai_org;
        $this->ai_region = $data->ai_region;
    }

    public function getArticleId(): string
    {
        return $this->article_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @return string[]
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function getCreator(): array
    {
        return $this->creator;
    }

    public function getVideoUrl(): ?string
    {
        return $this->video_url;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPubDate(): \DateTimeImmutable
    {
        return $this->pubDate;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function getSourceId(): string
    {
        return $this->source_id;
    }

    public function getSourcePriority(): int
    {
        return $this->source_priority;
    }

    public function getSourceUrl(): string
    {
        return $this->source_url;
    }

    public function getSourceIcon(): ?string
    {
        return $this->source_icon;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getCountry(): array
    {
        return $this->country;
    }

    public function getCategory(): array
    {
        return $this->category;
    }

    public function isDuplicate(): bool
    {
        return $this->duplicate;
    }

    public function getAiTag(): string
    {
        return $this->ai_tag;
    }

    public function getSentiment(): string
    {
        return $this->sentiment;
    }

    public function getSentimentStats(): string
    {
        return $this->sentiment_stats;
    }

    public function getAiRegion(): string
    {
        return $this->ai_region;
    }

    public function getAiOrg(): string
    {
        return $this->ai_org;
    }
}
