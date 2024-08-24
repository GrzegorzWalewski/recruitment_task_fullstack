<?php

namespace Integration\NBPExchangeRatesApi;

use App\Service\ExchangeRatesApiInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NBPExchangeRatesApiServiceTest extends WebTestCase
{
    public function testGetRatesSuccessfully(): void
    {
        $date = '2024-08-12';

        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/' . $date);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count(ExchangeRatesApiInterface::CURRENCIES), count($responseData));
    }

    public function testGetRatesWithNoData(): void
    {
        $date = '2024-01-01';

        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/'. $date);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);

        foreach ($responseData as $rate) {
            $this->assertEquals('No data available', $rate['buy']);
            $this->assertEquals('No data available', $rate['sell']);
        }
    }

    public function testGetRatesForDateOutOfRange(): void
    {
        $date = '2022-12-29';
        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/'. $date);
        $this->assertResponseStatusCodeSame(400);
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);
        $this->assertEquals('Date out of scope', $responseData['message']);
    }

    public function testBuyPriceOnlyForLimitedCurrencies(): void
    {
        $date = '2024-08-12';

        $client = static::createClient();
        $client->request('GET', '/api/exchange-rates/'. $date);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), TRUE);

        foreach ($responseData as $rate) {
            if (in_array($rate['code'], ExchangeRatesApiInterface::BUY_CURRENCIES)) {
                $this->assertIsFloat($rate['buy']);
            } else {
                $this->assertEquals('-', $rate['buy']);
            }            
        }
    }
}