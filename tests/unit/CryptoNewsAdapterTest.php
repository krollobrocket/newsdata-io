<?php

namespace Cyclonecode\NewsDataIO\Tests\Unit;

use Cyclonecode\NewsDataIO\Adapters\CryptoNewsAdapter;
use Cyclonecode\NewsDataIO\Enums\Arguments;
use Cyclonecode\NewsDataIO\Tests\AbstractTestCase;
use GuzzleHttp\Client;
use Cyclonecode\NewsDataIO\NewsDataIO;
use Cyclonecode\NewsDataIO\Plugin\Settings\Settings;

class CryptoNewsAdapterTest extends AbstractTestCase
{
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();
        $client = new Client();
        $settings = new Settings(NewsDataIO::OPTION_NAME);
        $this->sut = new CryptoNewsAdapter($settings, $client);
    }

    public function testGetNews(): void
    {
        $response = $this->sut->getNews();
        $this->assertNull($response);
    }

    public function testGetAllowedArguments(): void
    {
        $this->assertIsArray($this->sut->getAllowedArguments());
    }

    public function testGetCryptoCoins(): void
    {
        $this->assertIsArray($this->sut->getCryptoCoins());
    }

    public function testGetCryptoCoinCodes(): void
    {
        $this->assertIsArray($this->sut->getCryptoCoinCodes());
    }

    public function testGetNewsByCoin(): void
    {
        $response = $this->sut->getNews([
            Arguments::ARG_COIN => 'btc',
        ]);
        $this->assertNull($response);
    }
}
