<?php

require_once "app/Procesare.php";
require_once 'app/Aplicatie.php';

function insertPercent($db, $data, $companyID) {

	$query = (
		"INSERT INTO `procent`
		(`idFirma`, `valoare`, `_from`, `_to`, `isNow`)
		VALUES
		(:companyID, :value, :fromDate, :toDate, :isNow)"
	);

	$stmt = $db->prepare($query);
	$ok = $stmt->execute([
		'companyID' => $companyID,
		'value' => $data['procent'],
		'fromDate' => DataCalendaristica::getZiuaPrecedenta(date("Y-m-1")),
		'toDate' => date("Y-m-d"),
		'isNow' => "1",
	]);

	if(!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}
}

function insertCompany($db, $data) {

	$query = (
		"INSERT INTO `firma`
		(`nume`, `localitate`, `dateContact`, `comentarii`)
		VALUES
		(:name, :address, :contactData, :comments)"
	);

	$stmt = $db->prepare($query);
	$ok = $stmt->execute([
		'name' => ucwords($data['nume']),
		'address' => ucwords($data['localitate']),
		'contactData' => ucwords($data['date_contact']),
		'comments' => $data['comentarii'],
	]);

	if(!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	return $db->lastInsertId();
}

$db = Aplicatie::getInstance()->Database;

try {
	Design::showHeader();

	$db->beginTransaction();

	$data			=   $_POST;

	Procesare::checkRequestedData(
		array('nume','localitate','procent'),
		$data,
		'add_company.php'
	);
	Procesare::createEmptyFields($data, array("comentarii","date_contact"));

	$companyID = insertCompany($db, $data);
	insertPercent($db, $data, $companyID);

	$db->commit();

	?>
	<div class="container">
		<div class="text-xs-center">
			<div class="text-success my-3">
				<h1>Firma a fost adăugată</h1>
			</div>
			<a class="btn btn-primary btn-lg" href="companies.php ">
				Înapoi
			</a>
		</div>
	</div>
	<?php
	DESIGN::showFooter();	?>

	<script type="text/javascript">
	(function(){

	})()
	</script>

	<?php
} catch (Exception $e) {
	$db->rollBack();
	DESIGN::complain($e->getMessage());
}
