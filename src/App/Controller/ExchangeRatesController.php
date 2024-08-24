<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ExchangeRatesApiInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\NoDataException;

class ExchangeRatesController extends AbstractController
{
  private $exchangeRatesApi;

  public function __construct(ExchangeRatesApiInterface $exchangeRatesApi)
  {
    $this->exchangeRatesApi = $exchangeRatesApi;
  }

  public function getExchangeRates(?string $date): Response
  {
    $date = ($date === null) ? 'today' : date('Y-m-d', strtotime($date));

    try {
      $rates = $this->exchangeRatesApi->getRatesForAllCurrencies($date);
    } catch (NoDataException $e) {
      foreach (ExchangeRatesApiInterface::CURRENCIES as $currency) {
        $rates[] = ['code' => $currency, 'buy' => 'No data available', 'sell' => 'No data available'];
      }
    }

    return new Response(
      json_encode($rates),
      Response::HTTP_OK,
      ['Content-type' => 'application/json']
    );
  }
}
