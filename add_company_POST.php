<?php

require_once "app/Procesare.php";
require_once 'app/Aplicatie.php';

function verifyCompany ($data) {
	function checkName($value) {
		$length = strlen($value);

		$isWrong = (
			$length < 5 ||
			$length > 30
		);

		if($isWrong) {
			throw new Exception("Denumirea are între 5 și 30 de caractere");
		}
	}

	function checkAddress($value) {
		$length = strlen($value);

		$isWrong = (
			$length < 5 ||
			$length > 30
		);

		if($isWrong) {
			throw new Exception("Adresa are între 5 și 30 de caractere");
		}
	}

	function checkCurrentPercent($raw) {
		$value = intval($raw);

		$isWrong = (
			!is_numeric($raw) ||
			$value < 0 ||
			$value > 100
		);

		if($isWrong) {
			throw new Exception("Procentul este între 0 și 100%");
		}
	}

	function checkComments($value) {
		$length = strlen($value);

		$isWrong = (
			$length > 30
		);

		if($isWrong) {
			throw new Exception("Comentariile au maxim 30 de caractere");
		}
	}

	function checkContactDetails($value) {
		$length = strlen($value);

		$isWrong = (
			$length > 30
		);

		if($isWrong) {
			throw new Exception("Datele de contact au maxim 30 de caractere");
		}
	}

	checkName($data["nume"]);
	checkAddress($data["localitate"]);
	checkCurrentPercent($data["procent"]);
	checkComments($data["comentarii"]);
	checkContactDetails($data["date_contact"]);
}

function insertPercent($db, $data, $companyID) {

	$query = (
		"INSERT INTO `procent`
		(`idFirma`, `valoare`, `_from`, `_to`, `isNow`)
		VALUES
		(:companyID, :value, :fromDate, :toDate, :isNow)"
	);

	$value = intval($data['procent']);

	if($value < 0 || $value > 100) {
		throw new Exception("Procentul este cuprins între 1 și 100");
	}

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

$db = null;

try {
	GLOBAL $db;
	$db = Aplicatie::getInstance()->Database;

	Design::showHeader();

	$db->beginTransaction();

	$data			=   $_POST;

	Procesare::checkRequestedData(
		array('nume','localitate','procent'),
		$data,
		'add_company.php'
	);
	Procesare::createEmptyFields($data, array("comentarii","date_contact"));

	verifyCompany($data);

	$companyID = insertCompany($db, $data);
	insertPercent($db, $data, $companyID);

	$db->commit();

	?>
	<div class="container">
		<div class="text-xs-center">
			<div class="text-success my-3">
				<h1>Firma a fost adăugată</h1>
			</div>
			<a id="back-button" class="btn btn-primary btn-lg" href="companies.php ">
				Înapoi
			</a>
		</div>
	</div>
	<?php
	DESIGN::showFooter();	?>

	<script type="text/javascript">
	(function(){
		$("#back-button").focus();
	})()
	</script>

	<?php
} catch (Exception $e) {
	$db->rollBack();
	DESIGN::complain($e->getMessage());
}
