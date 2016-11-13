<?php
require_once "Firma.php";
class FirmaOrganizatoare extends Firma {
	private $patron = null;
	public function FirmaOrganizatoare($db, $id) {

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
			throw new Exception("Ceva nu a mers așa cum trebuia");
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults == 0) {
			throw new Exception(sprintf("Firma organizatoare %d nu există", htmlspecialchars($id)));
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
