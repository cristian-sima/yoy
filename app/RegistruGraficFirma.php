<?php

require_once "app/Total.php";
require_once "app/Guvern.php";
require_once "app/Romanian.php";
require_once "app/FirmaSpatiu.php";
require_once "app/RegistruGrafic.php";
require_once "app/SituatieMecanica.php";
require_once "app/DataCalendaristica.php";
require_once "app/SituatieMecanicaTotaluri.php";

class RegistruGraficFirma extends RegistruGrafic {
  private $company = null;
  public function RegistruGraficFirma($company, $data) {
    $this->firma = $company;
    parent::__construct($data);
    parent::setTitle("REGISTRU DE CASĂ");
    parent::setPrimulRand($company->getDenumire() . ' din ' . $company->getLocatie());
  }
  public function getFirma() {
    return $this->firma;
  }
  protected function _processData() {
    $columns      = array(
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
        "width" => "100px"
      ),
      array(
        "content" => "EXPLICAȚII",
        "width" => "300px"
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
    $data_curenta = $this->getFrom();
    $incasari     = new Total("Încasări");
    $plati        = new Total("Plăți");
    $total        = new Total("General");
    $impozit      = new Total("Impozit");
    $dispoziții  = new Total("Dispoziții");
    $suma         = self::getSoldTotalLunar($this->getFirma(), new DataCalendaristica(DataCalendaristica::getZiuaPrecedenta($this->getFrom())));
    if ($suma > 0) {
      $total->actualizeazaIncasari($suma);
    } else {
      $total->actualizeazaPlati(-$suma);
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
      $situatie     = new SituatieMecanicaTotaluri($data_curenta, $data_curenta, $this->firma);
      if ($situatie->isCompletata()) {
        if ($situatie->getTotalIncasari() != 0) {
          $this->addRow(array(
            $this->getIndexNewRow(),
            "",
            $data_curenta->romanianFormat(),
            "ÎNCASĂRI",
            $situatie->getTotalIncasari(),
            0
          ));
        }
        $incasari->actualizeazaIncasari($situatie->getTotalIncasari());
      }

      $db = Aplicatie::getInstance()->Database;

      $query     = (
        "SELECT d.id, d.data, d._to, d.tip, d.valoare, d.document, d.explicatie,
        (
          SELECT nume FROM `firma` AS f WHERE f.id = d._to
        ) AS denumire_firma
        FROM dispozitie AS d
        WHERE  data=:date  AND _to=:to "
      );

      $stmt = $db->prepare($query);
      $ok = $stmt->execute([
        'date' => $data_curenta,
        'to' => $this->getFirma()->getID()
      ]);

      if(!$ok) {
        throw new Exception("Ceva nu a mers așa cum trebuia");
      }

      foreach($stmt as $dispozitie) {
        if ($dispozitie['tip'] == "plata") {
          $this->addRow([
            $this->getIndexNewRow(),
            htmlspecialchars($dispozitie['document']),
            $data_curenta->romanianFormat(),
            "DISPOZIȚIE ÎNCASARE",
            $dispozitie['valoare'],
            0
          ]);
          $dispoziții->actualizeazaIncasari($dispozitie['valoare']);
        } else {
          $this->addRow([
            $this->getIndexNewRow(),
            htmlspecialchars($dispozitie['document']),
            $data_curenta->romanianFormat(),
            "DISPOZIȚIE PLATĂ",
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
  public static function getSoldTotalLunar(Firma $company, DataCalendaristica $data) {
    $_total = 0;

    $db = Aplicatie::getInstance()->Database;

    $query = (
      "SELECT valoare
      FROM sold_inchidere_luna
      WHERE
      data_>=:firstDayOfMonth AND data_<= :lastDayOfMonth AND idFirma = :companyID"
    );

    $stmt = $db->prepare($query);
    $ok = $stmt->execute([
      'firstDayOfMonth' => $data->getFirstDayOfMonth(),
      'lastDayOfMonth' => $data->getLastDayOfMonth(),
      'companyID' => $company->getID()
    ]);

    if(!$ok) {
      throw new Exception("Ceva nu a mers așa cum trebuia");
    }

    foreach($stmt as $row) {
      $_total += intval($row['valoare']);
    }
    return $_total;
  }
}
