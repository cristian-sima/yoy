<?php
require_once "Firma.php";
class FirmaOrganizatoare extends Firma {
  private $patron = null;
  public function FirmaOrganizatoare($MYSQL, $id) {

		$db = $MYSQL->getResource();

		$query = (
		  "SELECT nume AS denumire, patron, localitate AS locatie
			FROM `firma_organizatoare`
			WHERE id=:id
			LIMIT 0,1"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute(array(
		  'id' => $id
		));

		if(!$ok) {
		  throw new Exception("Această firmă organizatoare nu există");
		}

		foreach($stmt as $row) {
			$this->denumire = $row['denumire'];
			$this->locatie  = $row['locatie'];
			$this->patron   = $row['patron'];
		}

    $this->id = $id;
  }
  public function getPatron() {
    return $this->patron;
  }
}
