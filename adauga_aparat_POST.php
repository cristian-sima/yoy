<?php

require_once "app/Aparat.php";
require_once "app/Procesare.php";
require_once "app/Aplicatie.php";
require_once "app/FirmaSpatiu.php";
require_once "app/SituatieMecanicaGraficaCompletaAparatNou.php";

Design::showHeader();


$db = Aplicatie::getInstance()->Database;
$db->beginTransaction();

try {
	$data = $_POST;

	Procesare::checkRequestedData(array(
		'firma_id',
		'in_depozit',
		'serie',
		'nume',
		'factor_mecanic',
		'pret_impuls',
		'data_autorizatie',
		'data_inspectie',
		'observatii',
		'ordinea'
	), $data, 'adauga_aparat.php');

	$query  = (
		"INSERT INTO `aparat`	(
			`ordinea`,
			`data_inspectie`,
			`data_autorizatie`,
			`nume`,
			`serie`,
			`factor_mecanic`,
			`pret_impuls`,
			`observatii`,
			`id_firma`,
			`in_depozit`
		)

		VALUES (
			:order,
			:inspectionDate,
			:authorizationDate,
			:name,
			:serial,
			:mecanicalFactor,
			:impulsPrice,
			:observations,
			:companyID,
			:isInDepozit
		)"
	);

	$stmt = $db->prepare($query);

	$ok = $stmt->execute([
		'order' => $data['ordinea'],
		'inspectionDate' => $data['data_inspectie'],
		'authorizationDate' => $data['data_autorizatie'],
		'name' => $data['nume'],
		'serial' => $data['serie'],
		'mecanicalFactor' => $data['factor_mecanic'],
		'impulsPrice' => $data['pret_impuls'],
		'observations' => $data['observatii'],
		'companyID' => $data['firma_id'],
		"isInDepozit" => $data['in_depozit'],
	]);

	if (!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	// select the id

	$id = 0;

	$query = (
		"SELECT id
		FROM aparat
		ORDER BY id DESC
		LIMIT 0,1"
	);

	$stmt = $db->prepare($query);
	$ok = $stmt->execute();

	if (!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	foreach ($stmt as $row) {
		$id = $row['id'];
	}

	$device_ = new Aparat($id);
	$date_   = new DataCalendaristica(date("Y-m-d"));

	// add into its history

	$query   = "INSERT INTO `istoric_aparat` (
		`id_firma`,
		`id_aparat`,
		`from_`,
		`to_`,
		`is_now`
	)
	VALUES (
		:companyID,
		:deviceID,
		:theDate,
		:theDate,
		:isNow
	)";

	$stmt = $db->prepare($query);

	$ok = $stmt->execute([
		"companyID" => $data['firma_id'],
		"deviceID" => $device_->getID(),
		"theDate" => $date_,
		"isNow" => "1"
	]);

	if (!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	if ($data['firma_id'] == "0") {

		$db->commit();

		Design::showConfirmation(
			'<span class="confirmation">
			Aparatul a fost adăugat în depozit
			</span>
			<a href="aparate_din_depozit.php">Înapoi la depozit</a>'
		);
	} else {
		$firma    = new FirmaSpatiu($data['firma_id']);
		$situatie = new SituatieMecanicaGraficaCompletaAparatNou($firma, $device_, $data['mecanic_intrare'], $data['mecanic_iesire']);

		$aparate  = $situatie->getAparate();

		if ($situatie->isCompletata()) {
			$id_completare = $situatie->getIDCompletare();
			$mysql = (
				"INSERT INTO index_mecanic (
					`id_aparat`,
					`id_completare`,
					`start_intrari`,
					`end_intrari`,
					`start_iesiri`,
					`end_iesiri`
				)
				VALUES (
					:deviceID,
					:completionID,
					:mechanicalIn,
					:mechanicalIn,
					:mechanicalOut,
					:mechanicalOut
				)"
			);

			$stmt = $db->prepare($query);

			$ok = $stmt->execute([
				"deviceID" => $device_->getID(),
				"completionID" => $id_completare,
				"mechanicalIn" => $data['mecanic_intrare'],
				"mechanicalOut" => $data['mecanic_iesire'],
			]);

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

		} else {
			$query = (
				"INSERT INTO `completare_mecanica`	(
					`id_firma`,
					`data_`,
					`autor`
				)
				VALUES (
					:companyID,
					:theDate,
					:author
				)"
			);

			$stmt = $db->prepare($query);

			$ok = $stmt->execute([
				"companyID" =>  $data['firma_id'],
				"theDate" => $date_,
				"author" => Aplicatie::getInstance()->getUtilizator()->getID()
			]);

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			$id_completare = 0;

			$query = (
				"SELECT id
				FROM completare_mecanica
				WHERE id_firma=:companyID AND data_=:theDate AND autor=:author "
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute([
				"companyID" => $data['firma_id'],
				"theDate" => $date_,
				"author" => Aplicatie::getInstance()->getUtilizator()->getID()
			]);

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach ($stmt as $row) {
				$id_completare = $row['id'];
			}

			// start creating query

			$query = (
				"INSERT INTO index_mecanic (
					`id_aparat`,
					`id_completare`,
					`start_intrari`,
					`end_intrari`,
					`start_iesiri`,
					`end_iesiri`
				)
				VALUES (
					:deviceID,
					:completionID,
					:inputStart,
					:inputEnd,
					:outputStart,
					:outputEnd
				)"
			);

			$stmt = $db->prepare($query);

			foreach ($aparate as $device) {
				$ok = $stmt->execute([
					":deviceID" => $device['data']->getID(),
					"completionID" => $id_completare,
					"inputStart" => $device['situatie']['start_intrari'],
					"inputEnd" => $device['situatie']['end_intrari'],
					"outputStart" => $device['situatie']['start_iesiri'],
					"outputEnd" => $device['situatie']['end_iesiri'],
				]);

				if (!$ok) {
					throw new Exception("Ceva nu a mers cum trebuia");
				}
			}
		}

		$db->commit();

		if ($data['firma_id'] != "0") {
			Design::showConfirmation('<span class="confirmation">Aparatul a fost adaugăt la firmă !</span> <a href="aparate.php?id=' . $_POST['firma_id'] . ' ">Înapoi</a>');
		}
	}
}
catch (Exception $e) {

	$db->rollBack();

	Design::showError($e->getMessage());
}
Design::showFooter();
?>
