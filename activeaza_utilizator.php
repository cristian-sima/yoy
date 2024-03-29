<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Design::showHeader();


try {
	$data = $_GET;

	Procesare::checkRequestedData(array(
		'id_user',
		'type'
	), $data, 'utilizatori.php');

	$db         = Aplicatie::getInstance()->Database;
	$utilizator = new Utilizator($db, $data['id_user']);

	$query = (
		"UPDATE  utilizator
		SET activ=:active
		WHERE id=:userID "
	);

	$stmt = $db->prepare($query);
	$ok = $stmt->execute(array(
		'active' => $data['type'],
		'userID' => $utilizator->getID()
	));

	if(!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	Design::showConfirmation('<span class="confirmation">Datele au fost modificate</span> <a href="utilizatori.php ">Înapoi la utilizatori</a>');
}
catch (Exception $e) {
	Design::showError($e->getMessage());
}
Design::showFooter();
?>
