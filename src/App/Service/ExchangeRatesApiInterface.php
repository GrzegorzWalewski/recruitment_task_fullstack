<?php

namespace App\Service;

interface ExchangeRatesApiInterface
{
  public const CURRENCIES = ['EUR', 'USD', 'CZK', 'IDR', 'BRL'];
  public const BUY_CURRENCIES = ['EUR', 'USD'];
  public const SMALL_FEE_CURRENCIES = ['EUR', 'USD'];
  public const SMALL_BUY_FEE = 0.05;
  public const SMALL_SELL_FEE = 0.07;
  public const NORMAL_SELL_FEE = 0.15;
  public const ROUND_AFTER = 4;

  public function getRatesForAllCurrencies(string $date): array;
}