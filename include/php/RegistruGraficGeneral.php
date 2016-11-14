<?php

require_once "include/php/Total.php";
require_once "include/php/Guvern.php";
require_once "include/php/Romanian.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/RegistruGrafic.php";
require_once "include/php/SituatieMecanica.php";
require_once "include/php/DataCalendaristica.php";
require_once "include/php/SituatieMecanicaTotaluri.php";

class RegistruGraficGeneral extends RegistruGrafic {
  public function RegistruGraficGeneral($data) {
    parent::__construct($data);
    parent::setTitle("REGISTRU GENERAL");
    parent::setPrimulRand(Aplicatie::getInstance()->getFirmaOrganizatoare()->getLocatie());
  }
  protected function _processData() {

    $db = Aplicatie::getInstance()->Database;

    $columns      = [
      [
        "content" => "NR. CRT",
        "width" => "50px"
      ], [
        "content" => "NR. <br /> ACT CASĂ",
        "width" => "125px"
      ], [
        "content" => "DATA",
        "width" => "90px"
      ], [
        "content" => "EXPLICAȚII",
        "width" => "310px"
      ], [
        "content" => "ÎNCASĂRI",
        "width" => "200px"
      ], [
        "content" => "PLĂȚI",
        "width" => "200px"
      ]
    ];
    $data_curenta = $this->getFrom();
    $incasari     = new Total("Încasări");
    $plati        = new Total("Plăți");
    $total        = new Total("General");
    $impozit      = new Total("Impozit");
    $dispoziții  = new Total("Dispoziții");
    $suma         = self::getSoldTotalLunar(new DataCalendaristica(DataCalendaristica::getZiuaPrecedenta($this->getFrom())));
    if ($suma > 0) {
      $total->actualizeazaIncasari($suma);
    } else {
      $total->actualizeazaPlati(-$suma);
    }
    parent::setColumns($columns);
    parent::setColoaneTotalizate([4, 5]);
    parent::setTotalTitleColumn(3);
    parent::setSumeColoaneTotalizate([
      4 => $total->getIncasari(),
      5 => $total->getPlati()
    ]);
    while (strtotime($data_curenta) <= strtotime($this->getTo())) {
      $_aparate_mecanice = new Total("Temporar");
      $_impozit          = new Total("Temporar");
      $_dispozitii       = new Total("Temporar");
      $data_curenta      = new DataCalendaristica($data_curenta);

      $query = (
        "SELECT id_firma
        FROM completare_mecanica
        WHERE data_=:theDate
        GROUP BY id_firma"
      );

      $stmt = $db->prepare($query);
      $ok    = $stmt->execute([
        "theDate" => $data_curenta,
      ]);

      if (!$ok) {
        throw new Exception("Ceva nu a mers cum trebuia");
      }

      foreach ($stmt as $row) {
        $firma    = new FirmaSpatiu($row['id_firma']);
        $situatie = new SituatieMecanicaTotaluri($data_curenta, $data_curenta, $firma);
        if ($situatie->isCompletata()) {
          $_aparate_mecanice->actualizeazaIncasari($situatie->getTotalIncasari());
        }
      }
      if ($_aparate_mecanice->getIncasari() != 0) {
        $this->addRow([
          $this->getIndexNewRow(),
          "",
          $data_curenta->romanianFormat(),
          "ÎNCASĂRI",
          $_aparate_mecanice->getIncasari(),
          0
        ]);
      }
      $plati->actualizeazaIncasari($_aparate_mecanice->getPlati());
      $incasari->actualizeazaIncasari($_aparate_mecanice->getIncasari());
      $query     = (
        "SELECT
        d.id,
        d.data,
        d._to,
        d.tip,
        d.valoare,
        d.document,
        d.explicatie,
        ( SELECT nume FROM `firma` AS f WHERE f.id = d._to ) AS denumire_firma
        FROM dispozitie AS d
        WHERE  data=:theDate
        ORDER by d.id"
      );

      $stmt2 = $db->prepare($query);
      $ok    = $stmt2->execute([
        "theDate" => $data_curenta,
      ]);

      if (!$ok) {
        throw new Exception("Ceva nu a mers cum trebuia");
      }

      foreach ($stmt2 as $dispozitie) {
        if ($dispozitie['tip'] == "plata") {
          $this->addRow([
            $this->getIndexNewRow(),
            htmlspecialchars($dispozitie['document']),
            $data_curenta->romanianFormat(),
            "DISP INCASARE DE LA CASA SPRE " . $dispozitie['denumire_firma'],
            $dispozitie['valoare'],
            0
          ]);
          $dispoziții->actualizeazaIncasari($dispozitie['valoare']);
        } else {
          $this->addRow([
            $this->getIndexNewRow(),
            htmlspecialchars($dispozitie['document']),
            $data_curenta->romanianFormat(),
            "DISP. PLATA DE LA " . $dispozitie['denumire_firma'] . ' SPRE CASA',
            0,
            $dispozitie['valoare']
          ]);
          $dispoziții->actualizeazaPlati($dispozitie['valoare']);
        }
      }
      $data_curenta = new DataCalendaristica(DataCalendaristica::getZiuaUrmatoare($data_curenta));
    }
    $total->actualizeazaIncasari($impozit->getIncasari());
    $total->actualizeazaIncasari($incasari->getIncasari());
    $total->actualizeazaIncasari($dispoziții->getIncasari());
    $total->actualizeazaPlati($dispoziții->getPlati());
    $total->actualizeazaPlati($plati->getIncasari());
    $this->addTotal($plati);
    $this->addTotal($incasari);
    $this->addTotal($impozit);
    $this->addTotal($dispoziții);
    $this->addTotal($total);
    $this->actualizeazaIncasari($total->getIncasari());
    $this->actualizeazaPlati($total->getPlati());
  }

  public static function getSoldTotalLunar(DataCalendaristica $data) {

    $db = Aplicatie::getInstance()->Database;
    $_total = 0;

    $query = (
      "SELECT valoare
      FROM sold_inchidere_luna
      WHERE data_>=:firstDayOfMonth AND data_<=:lastDayOfMonth "
    );

    $stmt = $db->prepare($query);
    $ok    = $stmt->execute([
      "firstDayOfMonth" => $data->getFirstDayOfMonth(),
      "lastDayOfMonth" => $data->getLastDayOfMonth(),
    ]);

    if (!$ok) {
      throw new Exception("Ceva nu a mers cum trebuia");
    }

    foreach ($stmt as $row) {
      $_total += intval($row['valoare']);
    }

    return $_total;
  }
}
