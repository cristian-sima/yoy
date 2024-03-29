<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Design::showHeader();


$db = Aplicatie::getInstance()->Database;

try {
	$data = $_POST;

	Procesare::checkRequestedData(array(
		'from',
		'to',
		'tip',
		'valoare'
	), $data, 'adauga_interval_taxa?type=' . $_POST['tip']);

	$isNow = 0;

	if ($data['to'] == "") {
		$data['to'] = DataCalendaristica::getZiuaUrmatoare(date("Y-m-d"));
		$isNow      = 1;
	}

	if (strtotime($data['from']) >= strtotime($data['to'])) {
		throw new Exception("Prima dată trebuie să fie mai mare decât a doua !");
	}

	$query      = (
		"INSERT INTO `taxa`
		(`tip`, `_from`, `_to`, `valoare`,`isNow`)
		VALUES (:type, :fromDate, :toDate, :value, :isNow)"
	);

	$stmt = $db->prepare($query);
	$ok    = $stmt->execute([
		"type" => $data['tip'],
		"fromDate" => $data['from'],
		"toDate" => $data['to'],
		"value" => $data['valoare'],
		"isNow" => $isNow,
	]);

	if (!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	Design::showConfirmation(
		'<span class="confirmation">Taxa a fost adaugată</span>
		<a href="setari.php">Înapoi</a>
		'
	);
}

catch (Exception $e) {
	Design::showError($e->getMessage());
}

Design::showFooter();
