<?php

require_once "include/php/Total.php";
require_once "include/php/Guvern.php";
require_once "include/php/Romanian.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/RegistruGrafic.php";
require_once "include/php/SituatieMecanica.php";
require_once "include/php/DataCalendaristica.php";

class RegistruGraficCentral extends RegistruGrafic {
  public function RegistruGraficCentral($data) {
    parent::__construct($data);
    parent::setTitle("REGISTRU CENTRAL");
    parent::setPrimulRand(Aplicatie::getInstance()->getFirmaOrganizatoare()->getLocatie());
  }
  protected function _processData() {
    $columns        = array(
      array(
        "content" => "NR. CRT",
        "width" => "50px"
      ),
      array(
        "content" => "NR. <br /> ACT CASĂ",
        "width" => "125px"
      ),
      array(
        "content" => "DATA",
        "width" => "90px"
      ),
      array(
        "content" => "EXPLICAȚII",
        "width" => "310px"
      ),
      array(
        "content" => "ÎNCASĂRI",
        "width" => "200px"
      ),
      array(
        "content" => "PLĂȚI",
        "width" => "200px"
      )
    );
    $data_curenta   = $this->getFrom();
    $sold_precedent = new Total("Sold precedent");
    $deconturi      = new Total("Deconturi");
    $depuneri       = new Total("Depuneri");
    $dispoziții    = new Total("Dispoziții");
    $total          = new Total("General");
    $suma           = self::getSoldTotalLunar(new DataCalendaristica(DataCalendaristica::getZiuaPrecedenta($this->getFrom())));
    if ($suma > 0) {
      $total->actualizeazaIncasari($suma);
      $sold_precedent->actualizeazaIncasari($suma);
    } else {
      $total->actualizeazaPlati(-$suma);
      $sold_precedent->actualizeazaIncasari($suma);
    }
    parent::setColumns($columns);
    parent::setColoaneTotalizate(array(
      4,
      5
    ));
    parent::setTotalTitleColumn(3);
    parent::setSumeColoaneTotalizate(array(
      4 => $total->getIncasari(),
      5 => $total->getPlati()
    ));
    while (strtotime($data_curenta) <= strtotime($this->getTo())) {
      $data_curenta = new DataCalendaristica($data_curenta);
      $query        = "SELECT
						d.id,
						d.data,
						d._to,
						d.tip,
						d.valoare,
						d.document,
						d.explicatie,
						(SELECT nume FROM `firma` AS f WHERE f.id = d._to) AS denumire_firma
					FROM dispozitie AS d
					WHERE  data='" . $data_curenta . "'
					ORDER by d.id";
      $result_zi    = mysql_query($query, Aplicatie::getInstance()->Database);
      while ($dispozitie = mysql_fetch_array($result_zi)) {
        if ($dispozitie['tip'] == "incasare") {
          $this->addRow(array(
            $this->getIndexNewRow(),
            htmlspecialchars($dispozitie['document']),
            $data_curenta->romanianFormat(),
            "DISP. INCASARE <small>DE LA</small> " . $dispozitie['denumire_firma'],
            $dispozitie['valoare'],
            0
          ));
          $dispoziții->actualizeazaIncasari($dispozitie['valoare']);
        } else {
          $this->addRow(array(
            $this->getIndexNewRow(),
            htmlspecialchars($dispozitie['document']),
            $data_curenta->romanianFormat(),
            "DISP. PLATA <small>SPRE</small> " . $dispozitie['denumire_firma'],
            0,
            $dispozitie['valoare']
          ));
          $dispoziții->actualizeazaPlati($dispozitie['valoare']);
        }
      }
      $mysql  = "SELECT 	data,
							suma,
							document,
							explicatie
				FROM depunere_numerar
				WHERE data = '" . $data_curenta . "'";
      $result = mysql_query($mysql, Aplicatie::getInstance()->Database);
      while ($depunere = mysql_fetch_array($result)) {
        $this->addRow(array(
          $this->getIndexNewRow(),
          htmlspecialchars($depunere['document']),
          $data_curenta->romanianFormat(),
          "DEPUNERE NUMERAR",
          0,
          $depunere['suma']
        ));
        $depuneri->actualizeazaIncasari($depunere['suma']);
      }
      $mysql = "SELECT 	data,
							suma,
							document,
							explicatie
					FROM decont
					WHERE data = '" . $data_curenta . "'";
      $_db   = mysql_query($mysql, Aplicatie::getInstance()->Database);
      while ($decont = mysql_fetch_array($_db)) {
        $this->addRow(array(
          $this->getIndexNewRow(),
          htmlspecialchars($decont['document']),
          $data_curenta->romanianFormat(),
          "DECONT - " . htmlspecialchars($decont['explicatie']),
          0,
          $decont['suma']
        ));
        $deconturi->actualizeazaIncasari($decont['suma']);
      }
      $data_curenta = new DataCalendaristica(DataCalendaristica::getZiuaUrmatoare($data_curenta));
    }
    $total->actualizeazaPlati($deconturi->getIncasari());
    $total->actualizeazaPlati($depuneri->getIncasari());
    $total->actualizeazaPlati($dispoziții->getPlati());
    $total->actualizeazaIncasari($dispoziții->getIncasari());
    $this->addTotal($sold_precedent);
    $this->addTotal($deconturi);
    $this->addTotal($depuneri);
    $this->addTotal($dispoziții);
    $this->addTotal($total);
    $this->actualizeazaIncasari($total->getIncasari());
    $this->actualizeazaPlati($total->getPlati());
  }
  public static function getSoldTotalLunar(DataCalendaristica $data) {
    $_total = 0;
    $q      = "SELECT 	valoare
		FROM sold_inchidere_luna
		 WHERE idFirma = '0' AND data_>='" . $data->getFirstDayOfMonth() . "' AND data_<= '" . $data->getLastDayOfMonth() . "'";
    $result = mysql_query($q, Aplicatie::getInstance()->Database);
    while ($db = mysql_fetch_array($result)) {
      $_total += intval($db['valoare']);
    }
    return $_total;
  }
}
