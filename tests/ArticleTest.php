<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Cyclonecode\NewsDataIO\Responses\Article;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    protected Article $article;

    public function setUp(): void
    {
        parent::setUp();
        $body = file_get_contents(__DIR__ . '/data/latest-size-5.json');
        $mock = new MockHandler([new Response(200, [], $body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler,
        ]);
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $adapter = new \Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter($settings, $client);
        $io = new NewsDataIO($settings);
        $this->article = $adapter->getNews()->getResults()[0];
    }

    public function testGetTitle(): void
    {
        $this->assertIsString($this->article->getTitle());
    }

    public function testGetDescription(): void
    {
        $this->assertNull($this->article->getDescription());
    }

    public function testGetLink(): void
    {
        $this->assertIsString($this->article->getLink());
    }

    public function testGetCreator(): void
    {
        $this->assertIsArray($this->article->getCreator());
    }

    public function testGetSourceId(): void
    {
        $this->assertIsString($this->article->getSourceId());
    }

    public function testGetSourceUrl(): void
    {
        $this->assertIsString($this->article->getSourceUrl());
    }

    public function testGetSourcePriority(): void
    {
        $this->assertIsInt($this->article->getSourcePriority());
    }

    public function testGetSourceIcon(): void
    {
        $this->assertIsString($this->article->getSourceIcon());
    }

    public function testGetContent(): void
    {
        $this->assertIsString($this->article->getContent());
    }

    public function testGetCountry(): void
    {
        $this->assertIsArray($this->article->getCountry());
    }

    public function testGetLanguage(): void
    {
        $this->assertIsString($this->article->getLanguage());
    }

    public function testGetCategory(): void
    {
        $this->assertIsArray($this->article->getCategory());
    }

    public function testGetKeywords(): void
    {
        $this->assertIsArray($this->article->getKeywords());
    }

    public function testGetPubDate(): void
    {
        $this->assertInstanceOf(DateTimeImmutable::class, $this->article->getPubDate());
    }

    public function testGetAiOrg(): void
    {
        $this->assertIsString($this->article->getAiOrg());
    }

    public function testGetAiRegion(): void
    {
        $this->assertIsString($this->article->getAiRegion());
    }

    public function testGetAiTag(): void
    {
        $this->assertIsString($this->article->getAiTag());
    }

    public function testGetSentiment(): void
    {
        $this->assertIsString($this->article->getSentiment());
    }

    public function testGetSentimentStats(): void
    {
        $this->assertIsString($this->article->getSentimentStats());
    }
}
