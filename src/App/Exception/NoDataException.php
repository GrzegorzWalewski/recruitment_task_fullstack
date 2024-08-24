<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NoDataException extends NotFoundHttpException
{
  public function __construct(string $message = "No data found for date specified", \Throwable $previous = null, int $code = 0)
  {
    parent::__construct($message, $previous, $code);
  }
}