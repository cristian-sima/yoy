<?php
require_once "Firma.php";
class FirmaSpatiu extends Firma {
	private $activa;
	private $comentarii;
	private $dateContact;
	public function FirmaSpatiu($id) {

		$db = Aplicatie::getInstance()->Database;

		$query = (
			"SELECT dateContact, comentarii, nume AS denumire, localitate AS locatie, activa
			FROM `firma`
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
			throw new Exception(sprintf("Firma parteneră %d nu există", $id));
		}

		foreach($stmt as $row) {
			$this->denumire    = $row['denumire'];
			$this->locatie     = $row['locatie'];
			$this->activa      = $row['activa'];
			$this->comentarii  = $row['comentarii'];
			$this->dateContact = $row['dateContact'];
		}

		$this->id = $id;

	}
	public function isActiva() {
		return (($this->activa == '1') ? true : false);
	}
	public function getDateContact() {
		return $this->dateContact;
	}
	public function getComentarii() {
		return $this->comentarii;
	}
	public function getProcentFirma(DataCalendaristica $data) {

		$db = Aplicatie::getInstance()->Database;
		$exista  = false;
		$valoare = 0;

		$query      = (
			"SELECT valoare
			FROM procent
			WHERE
			idFirma=:companyID AND ((
				isNow='0' AND :firstDayOfMonth >= _from AND :lastDayOfMonth <= _to
			) OR (
				isNow='1' AND :firstDayOfMonth>=_from
			))
			LIMIT 1"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute(array(
			'companyID' => $this->id,
			'firstDayOfMonth' => $data->getFirstDayOfMonth(),
			'lastDayOfMonth' => $data->getLastDayOfMonth()
		));

		if(!$ok) {
			throw new Exception("Ceva nu a mers așa cum trebuia");
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults == 0) {
			throw new Exception(sprintf("Firma %d nu are un procent stabilit la data de %s", $id, $data));
		}

		foreach($stmt as $row) {
			$valoare = $row['valoare'];
			$exista  = true;
		}

		return $valoare;
	}
}
