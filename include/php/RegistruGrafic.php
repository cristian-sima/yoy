<?php
require_once "include/php/Registru.php";
abstract class RegistruGrafic extends Registru {
  private $rows = null;
  private $title = "";
  private $columns = null;
  private $totaluri = null;
  private $primulRand = "";
  private $indexNewRow = 1;
  private $columnsWeights = null;
  private $total_sum_columns = array();
  protected $totalTitleColumn = 0;
  protected $sumeRaportate = null;
  protected $sum_columns = array();
  public function RegistruGrafic(DataCalendaristica $data) {
    $this->rows     = array();
    $this->columns  = array();
    $this->totaluri = array();
    parent::__construct($data);
  }
  public function actualizeazaSumaTotalaColoana($coloana, $suma) {
    $this->total_sum_columns[$coloana] += $suma;
  }
  public function getSumaTotalaColoana($coloana) {
    return $this->total_sum_columns[$coloana];
  }
  public function setColoaneTotalizate(array $col) {
    $this->sum_columns = $col;
    foreach ($col as $coloana) {
      $this->total_sum_columns[$coloana] = 0;
    }
  }
  public function setSumeColoaneTotalizate(array $new) {
    $this->total_sum_columns = $new;
  }
  public function getTotalTitleColumn() {
    return $this->totalTitleColumn;
  }
  public function getTotaluriColoane() {
    return $this->sum_columns;
  }
  protected function getIndexNewRow() {
    return $this->indexNewRow;
  }
  protected function incrementIndexNewRow() {
    $this->indexNewRow++;
  }
  public function getNumarulDeRanduri() {
    return count($this->rows);
  }
  protected function setTotalTitleColumn($value) {
    $this->totalTitleColumn = $value;
  }
  public function getNumarulDeColoane() {
    return count($this->columns);
  }
  public function setColumns(array $columns) {
    $this->columns = $columns;
  }
  protected function addColumn(array $column) {
    array_push($this->columns, $column);
  }
  public function addRow(array $new_row) {
    array_push($this->rows, $new_row);
    $this->incrementIndexNewRow();
  }
  public function getRow($id) {
    $row = $this->rows[$id];
    return $row;
  }
  public function getColoana($id) {
    return $this->rows[$id];
  }
  public function getColoane() {
    return $this->columns;
  }
  public function getPrimulRand() {
    return $this->primulRand;
  }
  public function setPrimulRand($primulRand) {
    $this->primulRand = $primulRand;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function getDateTitle() {
    return DataCalendaristica::getNumeleLunii(parent::getFrom()->getLuna()) . ' ' . parent::getFrom()->getAnul();
  }
  protected function addTotal(Total $total) {
    array_push($this->totaluri, $total);
  }
  public function getTotaluri() {
    return $this->totaluri;
  }
}
