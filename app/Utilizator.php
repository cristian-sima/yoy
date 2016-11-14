<?php
class Utilizator {
  private $id;
  private $utilizator;
  private $nume;
  private $tipCont;
  private $id_firma;
  private $activ;
  private $tip_operator;
  public function Utilizator($db, $id) {

		$query = (
		  "SELECT user AS utilizator, nume, tipCont, idFirma as id_firma, activ, tipOperator
			FROM `utilizator`
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
			throw new Exception(sprintf("Utilizatorul %d nu există", htmlspecialchars($id)));
		}

		foreach($stmt as $row) {
			$this->utilizator   = $row['utilizator'];
			$this->nume         = $row['nume'];
			$this->activ        = $row['activ'];
			$this->tipCont      = $row['tipCont'];
			$this->id_firma     = $row['id_firma'];
			$this->tip_operator = $row['tipOperator'];
		}

    $this->id = $id;
  }
  public function getID() {
    return $this->id;
  }
  public function getUtilizator() {
    return $this->utilizator;
  }
  public function getNume() {
    return $this->nume;
  }
  public function isActiv() {
    return $this->activ;
  }
  public function getTipCont() {
    return $this->tipCont;
  }
  public function getIDFirma() {
    return $this->id_firma;
  }
  public function getTipOperator() {
    return $this->tip_operator;
  }
  public function isAdministrator() {
    return ($this->tipCont == "admin");
  }
  public function isDesktop() {
    return ($this->tip_operator == "desktop");
  }
  public function isOperator() {
    return ($this->tipCont != "admin");
  }
}
