<?php
require_once "Situatie.php";

class SituatieMecanica extends Situație {
	private $numarulDeAparate = 0;
	private $id;
	public function __construct(DataCalendaristica $from, DataCalendaristica $to, Firma $firma) {
		parent::__construct($from, $to, $firma);
	}
	protected function _processData() {

		$nr_de_aparate = 0;
		$db = Aplicatie::getInstance()->Database;

		$query = (
			"SELECT
			MIN(indexi.start_intrari) AS start_intrari,
			MAX(indexi.end_intrari) AS end_intrari,
			MIN(indexi.start_iesiri) AS start_iesiri,
			MAX(indexi.end_iesiri) AS end_iesiri,
			aparat.factor_mecanic,
			aparat.id AS id_aparat,
			aparat.pret_impuls,
			indexi.id_aparat,
			completare.id AS id_situatie
			FROM `index_mecanic` AS indexi
			LEFT JOIN `completare_mecanica` AS completare
			ON completare.id = indexi.id_completare
			LEFT JOIN `aparat` AS aparat
			ON indexi.id_aparat = aparat.id
			WHERE
			exists	(
				SELECT id FROM istoric_aparat AS istoric
				WHERE  istoric.id_aparat = indexi.id_aparat    AND
				istoric.id_firma  = completare.id_firma AND
				(
					(istoric.is_now='0' AND istoric.from_ <= :dateFrom AND :dateTo <= istoric.to_) OR
					(istoric.is_now='0' AND istoric.to_   <= :dateTo   AND istoric.to_ >= :dateFrom) OR
					(istoric.is_now='0' AND istoric.from_ >= :dateFrom AND istoric.from_ <= :dateTo) OR
					(istoric.is_now='1' AND istoric.from_ <= :dateTo)
				)
			) AND (
				completare.id_firma =:companyID
			) AND (
				completare.data_ >= :dateFrom AND completare.data_ <= :dateTo
			)

			GROUP BY indexi.id_aparat
			ORDER BY completare.data_"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute(array(
			"dateFrom" => $this->getFrom(),
			"dateTo" => $this->getTo(),
			"companyID" => $this->getFirma()->getID()
		));

		if(!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		$nrOfResults = $stmt->rowCount();

		if($nrOfResults != 0) {
			$this->isCompletata = true;
		}

		foreach($stmt as $index) {
			$this->id      = $index["id_situatie"];
			$total_intrari = ($index['factor_mecanic'] * ($index['end_intrari'] - $index['start_intrari'])) * $index['pret_impuls'];
			$total_iesiri  = ($index['factor_mecanic'] * ($index['end_iesiri'] - $index['start_iesiri'])) * $index['pret_impuls'];

			$this->calculeazaTotal($total_intrari, $total_iesiri);

			$nr_de_aparate++;
		}

		$this->numarulDeAparate = $nr_de_aparate;
	}
	public function getId() {
		return $this->id;
	}
	public function getNumarulDeAparate() {
		return $this->numarulDeAparate;
	}
	public static function getUltimaCompletare(Firma $firma, DataCalendaristica $data) {
		return self::getZiuaCompletata($firma, $data, "<=", "DESC");
	}
	public static function getUltimaCompletareStrict(Firma $firma, DataCalendaristica $data) {
		return self::getZiuaCompletata($firma, $data, "<", "DESC");
	}
	public static function getUrmatoareaCompletare(Firma $firma, DataCalendaristica $data) {
		return self::getZiuaCompletata($firma, $data, ">=", "DESC");
	}
	public static function getUrmatoareaCompletareStrict(Firma $firma, DataCalendaristica $data) {
		return self::getZiuaCompletata($firma, $data, ">", "ASC");
	}
	private static function getZiuaCompletata($company, $date, $sign, $order) {

		$data_ = null;
		$db = Aplicatie::getInstance()->Database;

		$query = (
			"SELECT data_
			FROM `completare_mecanica`
			WHERE	id_firma=:companyID AND data_" . $sign . ":date
			ORDER BY data_ " . $order . "
			LIMIT 0,1"
		);

		$stmt = $db->prepare($query);
		$ok = $stmt->execute(array(
			"companyID" => $company->getID(),
			"date" => $date,
		));

		if(!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		foreach($stmt as $situatie) {
			$data_ = $situatie['data_'];
		}

		return $data_;
	}
}
