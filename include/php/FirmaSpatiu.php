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
		$valoare = 0;
		$exista  = false;
		$A2      = "SELECT valoare from procent WHERE idFirma='" . $this->id . "' AND  (( isNow='0' AND '" . $data->getFirstDayOfMonth() . "'>=_from AND  '" . $data->getLastDayOfMonth() . "<=_to ') OR ( isNow='1' AND '" . $data->getFirstDayOfMonth() . "'>=_from))  LIMIT 1";
		$result = mysql_query($A2, Aplicatie::getInstance()->Database) or die(mysql_error());
		if (mysql_num_rows($result) == 0) {
			echo '<br /><span style="color:red">Eroare: !!!! Firma nu are un procent stabilit !</span><br />';
			die();
		}
		while ($p = mysql_fetch_array($result)) {
			$valoare = $p['valoare'];
			$exista  = true;
		}
		if (!$exista)
		throw new Exception("Nu exista procent impus pentru " . $this . ' la data ' . $data);
		return $valoare;
	}
}
