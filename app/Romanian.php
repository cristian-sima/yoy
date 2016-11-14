<?php

require_once "app/Total.php";

abstract class Romanian {
  public static function currency($money) {
    return number_format($money, 2, ',', '.') . ' lei';
  }
}
