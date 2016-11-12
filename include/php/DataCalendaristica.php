<?php
class DataCalendaristica {
  private static $months = array('1' => 'Ianuarie', "2" => 'Februarie', "3" => 'Martie', "4" => 'Aprilie', "5" => 'Mai', "6" => 'Iunie', "7" => 'Iulie', "8" => 'August', "9" => 'Septembrie', "10" => 'Octombrie', "11" => 'Noiembrie', "12" => 'Decembrie');
  private $luna = null;
  private $anul = null;
  private $ziua = null;
  private $fdm = null;
  private $ldm = null;
  public function DataCalendaristica($data) {
    if (!(preg_match("/^[0-9]{4}-([1-9]|0[1-9]|1[0-2])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/", $data))) {
      throw new Exception("Data trebuie sa fie in formatul ANUL-LUNA-ZIUA. A fost data data [" . $data . ']');
    }
    $_exp       = explode("-", self::format($data));
    $this->anul = intval($_exp[0]);
    $this->luna = $_exp[1];
    $this->ziua = $_exp[2];
    $this->fdm  = ($this->anul . '-' . $this->luna . '-01');
    $this->ldm  = self::format((self::getZiuaPrecedenta((($this->luna == "12") ? ($this->anul + 1) : $this->anul) . "-" . (($this->luna == "12") ? 1 : (intval($this->luna) + 1)) . '-01')));
  }
  public function getAnul() {
    return $this->anul;
  }
  public function getLuna() {
    return $this->luna;
  }
  public function getZiua() {
    return $this->ziua;
  }
  public function __toString() {
    return $this->anul . '-' . $this->luna . '-' . $this->ziua;
  }
  public function getFirstDayOfMonth() {
    return $this->fdm;
  }
  public function getLastDayOfMonth() {
    return $this->ldm;
  }
  public function romanianFormat() {
    return $this->ziua . '-' . $this->luna . '-' . $this->anul;
  }
  public static function getZiuaPrecedenta($data) {
    $dt = strtotime($data . '');
    return date("Y-m-d", $dt - 86400);
  }
  public static function getZiuaUrmatoare($data) {
    return date('Y-m-d', strtotime($data . ' +1 day'));
  }
  public static function getNumeleLunii($luna) {
    return self::$months[intval($luna)];
  }
  public static function format($date) {
    $_exp = explode("-", $date);
    $anul = intval($_exp[0]);
    $luna = ((intval($_exp[1]) < 10) ? ("0" . (intval($_exp[1]))) : (intval($_exp[1])));
    $ziua = ((intval($_exp[2]) < 10) ? ("0" . (intval($_exp[2]))) : (intval($_exp[2])));
    return $anul . '-' . $luna . '-' . $ziua;
  }
}
?>
