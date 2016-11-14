<?php

require_once "DataCalendaristica.php";

class Aparat {
  private $id;
  private $nume;
  private $serie;
  private $firma;
  private $activ;
  private $factor_mecanic;
  private $pret_impuls;
  private $data_inspectie;
  private $data_autorizatie;
  private $inDepozit;
  public function Aparat($id) {

		$db = Aplicatie::getInstance()->Database;

		$query = (
			"SELECT in_depozit As inDepozit, ordinea, observatii, serie, nume, factor_mecanic, pret_impuls, id_firma, data_inspectie, data_autorizatie, activ
			FROM `aparat`
			WHERE id=:id
			LIMIT 0,1"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute(array(
			'id' => $id
		));

		if(!$ok) {
			throw new Exception("Ceva nu a mers așa cum trebuia");
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults == 0) {
			throw new Exception(sprintf("Aparatul %d nu există", htmlspecialchars($id)));
		}

		foreach($stmt as $row) {
      $this->serie            = $row['serie'];
      $this->nume             = $row['nume'];
      $this->factor_mecanic   = $row['factor_mecanic'];
      $this->pret_impuls      = $row['pret_impuls'];
      $this->firma            = $row['id_firma'];
      $this->data_inspectie   = $row['data_inspectie'];
      $this->data_autorizatie = $row['data_autorizatie'];
      $this->activ            = $row['activ'];
      $this->observatii       = $row['observatii'];
      $this->ordinea          = $row['ordinea'];
      $this->inDepozit        = $row['inDepozit'];
    }
    $this->id = $id;
  }
  public function getObservatii() {
    return $this->observatii;
  }
  public function getID() {
    return $this->id;
  }
  public function getOrdinea() {
    return $this->ordinea;
  }
  public function getFactorMecanic() {
    return $this->factor_mecanic;
  }
  public function getNume() {
    return $this->nume;
  }
  public function getSerie() {
    return $this->serie;
  }
  public function getPretImpuls() {
    return $this->pret_impuls;
  }
  public function getFirmaCurenta() {
    return $this->firma;
  }
  public function isActiv() {
    return (($this->activ == '1') ? true : false);
  }
  public function isInDepozit() {
    return (($this->inDepozit == '1') ? true : false);
  }
  public function getDataAutorizatie() {
    return $this->data_autorizatie;
  }
  public function getDataInspectie() {
    return $this->data_inspectie;
  }
}
