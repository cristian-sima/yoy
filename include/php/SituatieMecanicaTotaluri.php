<?php
require_once "Situatie.php";
class SituatieMecanicaTotaluri extends Situație {
	private $numarulDeAparate = 0;
	public function __construct(DataCalendaristica $from, DataCalendaristica $to, Firma $firma) {
		parent::__construct($from, $to, $firma);
	}
	protected function _processData() {
		$db = Aplicatie::getInstance()->Database;
		$query = (
			"SELECT	sum(situatie.total_incasari) as total_incasari
			FROM `completare_mecanica` AS situatie
			WHERE id_firma = :companyID AND	data_	>= :fromDate AND data_ <= :toDate
			LIMIT 0,1"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute([
			'companyID' => $this->getFirma()->getID(),
			'fromDate' => $this->getFrom(),
			'toDate' => $this->getTo()
		]);

		if(!$ok) {
			throw new Exception("Ceva nu a mers așa cum trebuia");
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults != 0) {
			$this->isCompletata = true;
		}

		foreach($stmt as $situatie) {
			$this->calculeazaTotal($situatie['total_incasari']);
		}
	}
}
