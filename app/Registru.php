<?php

require_once "app/Total.php";
require_once "app/DataCalendaristica.php";

abstract class Registru extends Total {
  private $from = null;
  private $to = null;
  private $total = null;
  public function Registru(DataCalendaristica $data) {
    $this->from = new DataCalendaristica($data->getFirstDayOfMonth());
    $this->to   = new DataCalendaristica($data->getLastDayOfMonth());
    parent::__construct("General");
    $this->_processData();
  }
  public function getFrom() {
    return $this->from;
  }
  public function getTo() {
    return $this->to;
  }
  protected abstract function _processData();
}
