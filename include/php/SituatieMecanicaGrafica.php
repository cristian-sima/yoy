<?php

require_once "Aparat.php";
require_once "Utilizator.php";
require_once "SituatieGrafica.php";

class SituatieMecanicaGrafica extends SituatieGrafica {
	private $idCompletare = null;
	private $filterFrom = null;
	private $filterTo = null;
	public function __construct($from, $to, $firma) {
		$this->filterFrom = $from;
		$this->filterTo   = $to;
		parent::__construct($from, $to, $firma, "mecanice");
	}
	public function getIDCompletare() {
		return $this->idCompletare;
	}
	protected function getFilterDateFrom() {
		return $this->filterFrom;
	}
	protected function setFilterFrom($filter) {
		$this->filterFrom = $filter;
	}
	protected function setFilterTo($filter) {
		$this->filterTo = $filter;
	}
	protected function getFilterDateTo() {
		return $this->filterTo;
	}
	protected function _processData() {
		$autor    = null;
		$activate = (($this->getFrom() == $this->getTo()) ? true : false);
		$db       = Aplicatie::getInstance()->Database;

		$query    = (
			"SELECT
			MIN(indexi.start_intrari) AS start_intrari,
			MAX(indexi.end_intrari) AS end_intrari,
			MIN(indexi.start_iesiri) AS start_iesiri,
			MAX(indexi.end_iesiri) AS end_iesiri,
			completare.autor,
			indexi.id_aparat,
			completare.id AS token
			FROM `index_mecanic` AS indexi
			LEFT JOIN `completare_mecanica` AS completare
			ON completare.id = indexi.id_completare
			LEFT JOIN `aparat` AS aparat
			ON indexi.id_aparat = aparat.id
			WHERE
			exists (
				SELECT id FROM istoric_aparat AS istoric
				WHERE  istoric.id_aparat = indexi.id_aparat    AND
				istoric.id_firma  = completare.id_firma AND
				(
					(istoric.is_now='0' AND istoric.from_ <= :filterDateFrom AND :filterDateTo <= istoric.to_) OR
					(istoric.is_now='0' AND istoric.to_   <= :filterDateTo   AND istoric.to_   >= :filterDateFrom) OR
					(istoric.is_now='0' AND istoric.from_ >= :filterDateFrom AND istoric.from_ <= :filterDateTo) OR
					(istoric.is_now='1' AND istoric.from_ <= :filterDateTo  )
				)
			) AND (
				completare.id_firma = :companyID
			) AND (
				completare.data_ >= :fromDate AND	completare.data_ <= :toDate
			)

			GROUP BY indexi.id_aparat
			ORDER by completare.data_,aparat.ordinea"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute([
			"companyID" => $this->getFirma()->getID(),
			"filterDateFrom" => $this->getFilterDateFrom(),
			"filterDateTo" => $this->getFilterDateTo(),
			"fromDate" => $this->getFrom(),
			"toDate" => $this->getTo()
		]);

		if(!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults != 0) {
			$this->isCompletata = true;
		}

		foreach($stmt as $situatie) {

			$aparat             = new Aparat($situatie['id_aparat']);

			$total_intrari      = ($aparat->getFactorMecanic() * ($situatie['end_intrari'] - $situatie['start_intrari'])) * $aparat->getPretImpuls();
			$total_iesiri       = ($aparat->getFactorMecanic() * ($situatie['end_iesiri'] - $situatie['start_iesiri'])) * $aparat->getPretImpuls();
			$dif1               = $aparat->getFactorMecanic() * ($situatie['end_intrari'] - $situatie['start_intrari']);
			$dif2               = $aparat->getFactorMecanic() * ($situatie['end_iesiri'] - $situatie['start_iesiri']);

			$this->idCompletare = $situatie['token'];
			$this->addAparat($aparat, array(
				"start_intrari" => $situatie['start_intrari'],
				"end_intrari" => $situatie['end_intrari'],
				"start_iesiri" => $situatie['start_iesiri'],
				"end_iesiri" => $situatie['end_iesiri'],
				"diferenta_1" => $dif1,
				"diferenta_2" => $dif2
			));
			$this->calculeazaTotal($total_intrari, $total_iesiri);
			$autor = $situatie['autor'];
		}

		try {
			$this->autor = new Utilizator($db, $autor);
		}

		catch (Exception $e) {
			$this->autor = null;
		}
	}
}
