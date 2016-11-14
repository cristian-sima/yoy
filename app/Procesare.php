<?php
class Procesare {
  public static function checkRequestedData($args, $array, $address) {
    foreach ($args as $arg) {
      if (!isset($array[$arg])) {
        throw new Exception("Câmpul " . $arg . " nu a fost completat ! <a href='" . $address . "'>Înapoi</a>");
      }
    }
  }
  public static function createEmptyFields(&$data, $toCheck) {
    foreach ($toCheck as $field) {
      if (!isset($data[$field])) {
        $data[$field] = '';
      }
    }
  }
}
