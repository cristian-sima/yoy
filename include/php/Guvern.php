<?php
require_once "DataCalendaristica.php";
class Guvern {
  public static function getTaxaDeAutorizareAparat(DataCalendaristica $data) {
    return (self::_doWork("aparat", $data));
  }
  private static function _doWork($type, DataCalendaristica $data) {
    $valoare = 0;
    $exist   = false;

    $db = Aplicatie::getInstance()->Database;

    $query       = (
      "SELECT valoare
      FROM taxa
      WHERE
      tip=:type AND ((
        isNow='0' AND _from >= :firstDayOfMonth AND _to <= :lastDayOfMonth
      ) OR (
        isNow='1' AND :firstDayOfMonth >= _from
      ))
      LIMIT 1"
    );

    $stmt = $db->prepare($query);
    $ok = $stmt->execute(array(
      "firstDayOfMonth" => $data->getFirstDayOfMonth(),
      "lastDayOfMonth" => $data->getLastDayOfMonth(),
      "type" => $type
    ));

    if(!$ok) {
      throw new Exception("Ceva nu a mers cum trebuia");
    }

    foreach($stmt as $taxa) {
      $valoare = $taxa['valoare'];
      $exist   = true;
    }
    if (!$exist) {
      die("Nu există o valoare stabilită pentru [#" . $type . "#] pentru data: " . $data);
    }
    return $valoare;
  }
}
