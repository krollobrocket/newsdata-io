<?php

namespace Cyclonecode\NewsDataIO\Tests\Unit;

use Cyclonecode\NewsDataIO\Adapters\LatestNewsAdapter;
use Cyclonecode\NewsDataIO\Enums\AITags;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Enums\Categories;
use Cyclonecode\NewsDataIO\Responses\NewsResponse;
use Cyclonecode\NewsDataIO\Tests\AbstractTestCase;
use GuzzleHttp\Client;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class LatestNewsAdapterTest extends AbstractTestCase
{
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();
        $client = new Client();
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $this->sut = new LatestNewsAdapter($settings, $client);
    }

    protected function createAndSetClient(?string $fileName): void
    {
        $body = file_get_contents(__DIR__ . '/data/' . $fileName);
        $mock = new MockHandler([new Response(200, [], $body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client([
            'handler' => $handler,
        ]);
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $this->sut = new LatestNewsAdapter($settings, $client);
    }

    public function testGetNewsError(): void
    {
        $this->createAndSetClient('latest-error.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
        ]);
        $this->assertNull($response);
    }

    public function testGetNews(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsFromDomain(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_DOMAIN => 'bbc',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsExcludeDomain(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_EXCLUDE_DOMAIN => 'bbc',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsDomainURL(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_DOMAIN_URL => 'bbc.com',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsDomainAndDomainURL(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_DOMAIN => 'bbc',
            Arguments::ARG_DOMAIN_URL => 'bbc.com',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsDomainAndExcludeDomain(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_DOMAIN => 'bbc',
            Arguments::ARG_EXCLUDE_DOMAIN => true,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsDomainURLAndExcludeDomain(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_DOMAIN_URL => 'bbc.com',
            Arguments::ARG_EXCLUDE_DOMAIN => true,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetFiveNews(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
        ]);
        $this->assertCount(5, $response->getResults());
    }

    public function testGetNewsFreeOverLimit(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => Arguments::MAX_SIZE_PREMIUM,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsPremiumOverLimit(): void
    {
        global $option;
        $option = [
            NewsDataIO::OPTION_NAME => [
                Arguments::ARG_APIKEY => 'pub_39467bf0d0fda18e6194809c1545718b1bb04',
                Arguments::ARG_PREMIUM_APIKEY => true,
            ],
        ];
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => Arguments::MAX_SIZE_PREMIUM * 2,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithLanguageCode(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_LANGUAGE => 'en',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithCountryCode(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_COUNTRY => 'se',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithCategoryCode(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE=> 5,
            Arguments::ARG_CATEGORY => Categories::CATEGORY_BUSINESS,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsExcludeCategory(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_EXCLUDE_CATEGORY => Categories::CATEGORY_CRIME,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsCategoryAndExcludeCategory(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_CATEGORY => Categories::CATEGORY_CRIME,
            Arguments::ARG_EXCLUDE_CATEGORY => Categories::CATEGORY_WORLD,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithTag(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_TAG => 'business',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithImages(): void
    {
        $this->createAndSetClient('latest-images.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_IMAGE => 1,
        ]);
        $response = array_filter($response->getResults(), fn ($item) => $item->getImageUrl() !== null);
        $this->assertCount(5, $response);
    }

    public function testGetNewsWithoutImages(): void
    {
        $this->createAndSetClient('latest-no-images.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_IMAGE => 0,
        ]);
        $response = array_filter($response->getResults(), fn ($item) => $item->getImageUrl() === null);
        $this->assertCount(5, $response);
    }

    public function _testGetNewsWithVideo(): void
    {
        $this->createAndSetClient('latest-videos.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_VIDEO => 1,
        ]);
        $response = array_filter($response->getResults(), fn ($item) => $item->getVideoUrl() !== null);
        $this->assertCount(5, $response);
    }

    public function testGetNewsWithoutVideo(): void
    {
        $this->createAndSetClient('latest-no-videos.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 5,
            Arguments::ARG_VIDEO => 0,
        ]);
        $response = array_filter($response->getResults(), fn ($item) => $item->getVideoUrl() === null);
        $this->assertCount(5, $response);
    }

    public function testGetNewsWithQueryAndQueryTitle(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_QUERY => 'foobar',
            Arguments::ARG_QUERY_TITLE => 'foobar',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithQueryAndQueryMeta(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_QUERY => 'foobar',
            Arguments::ARG_QUERY_META => 'foobar',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithQueryTitleAndQueryMeta(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_QUERY_TITLE => 'foobar',
            Arguments::ARG_QUERY_META => 'foobar',
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetNewsWithAITags(): void
    {
        $this->createAndSetClient('latest-size-5.json');
        $response = $this->sut->getNews([
            Arguments::ARG_SIZE => 1,
            Arguments::ARG_TAG => AITags::TAG_AVIATION,
        ]);
        $this->assertInstanceOf(NewsResponse::class, $response);
    }

    public function testGetCountries(): void
    {
        $response = $this->sut->getCountries();
        $this->assertIsArray($response);
    }

    public function testGetCountryCodes(): void
    {
        $response = $this->sut->getCountryCodes();
        $this->assertIsArray($response);
    }

    public function testGetLanguages(): void
    {
        $response = $this->sut->getLanguages();
        $this->assertIsArray($response);
    }

    public function testGetLanguageCodes(): void
    {
        $response = $this->sut->getLanguageCodes();
        $this->assertIsArray($response);
    }

    public function testGetCategories(): void
    {
        $response = $this->sut->getCategories();
        $this->assertIsArray($response);
    }

    public function testGetCategoryCodes(): void
    {
        $response = $this->sut->getCategoryCodes();
        $this->assertIsArray($response);
    }

    public function tesGetAllowedArguments(): void
    {
        $this->assertIsArray($this->sut->getAllowedArguments());
    }

    public function testGetAITags(): void
    {
        $this->assertIsArray($this->sut->getAITags());
    }

    public function testGetAITagCodes(): void
    {
        $this->assertIsArray($this->sut->getAITagCodes());
    }
}
