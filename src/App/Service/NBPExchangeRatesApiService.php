<?php

namespace App\Service;

use App\Exception\NoDataException;
use App\Service\ExchangeRatesApiInterface;
use Exception;
use Symfony\Component\HttpClient\HttpClient;

class NBPExchangeRatesApiService implements ExchangeRatesApiInterface
{
    public function getRatesForAllCurrencies(string $date): array
    {
      $result = [];

      $rates = $this->getExchangeRates($date);

      foreach ($rates as $rate) {
        $code = $rate['code'];

        if (!in_array($code, ExchangeRatesApiInterface::CURRENCIES)) {
          continue;
        }

        if (in_array($code, ExchangeRatesApiInterface::BUY_CURRENCIES)) {
          $buyRate = $this->getBuyRate($rate) ?? '-';
        } else {
          $buyRate = '-';
        }

        $sellRate = $this->getSellRate($rate) ?? '-';

        $result[] = ['code' => $code, 'name' => $rate['currency'], 'buy' => $buyRate, 'sell'=> $sellRate];
      }

      return $result;
    }

    private function getSellRate(array $rate): float
    {
      if (in_array($rate['code'], ExchangeRatesApiInterface::SMALL_FEE_CURRENCIES)) {
        return round($rate['mid'] + ExchangeRatesApiInterface::SMALL_SELL_FEE, ExchangeRatesApiInterface::ROUND_AFTER);
      }

      return round($rate['mid'] + ExchangeRatesApiInterface::NORMAL_SELL_FEE, ExchangeRatesApiInterface::ROUND_AFTER);
    }

    private function getBuyRate(array $rate): ?float
    {
      if (in_array($rate['code'], ExchangeRatesApiInterface::SMALL_FEE_CURRENCIES)) {
        return round((float) $rate['mid'] + ExchangeRatesApiInterface::SMALL_BUY_FEE, ExchangeRatesApiInterface::ROUND_AFTER);
      }
    }

    private function getExchangeRates(string $date): array
    {
      $client = HttpClient::create();
      $response = $client->request('GET', "https://api.nbp.pl/api/exchangerates/tables/A/$date?format=json");
      $statusCode = $response->getStatusCode();

      try {
        $content = $response->getContent();
      } catch (Exception $e) {
          if ($statusCode === 404) {
            throw new NoDataException();
          }
      }

      if ($statusCode !== 200) {
        return [];
      }
      
      $result = json_decode($content, true);

      return $result[0]['rates'];
    }
}