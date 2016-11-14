<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->Database;

try {
	$data = $_POST;

	Procesare::checkRequestedData(array(
		'tipOperator',
		'nume',
		'user',
		'parola',
		'idFirma',
		'tipCont'
	), $data, 'utilizatori.php');

	$query = (
		"SELECT user
		FROM utilizator
		WHERE user=:user "
	);

	$stmt = $db->prepare($query);
	$ok    = $stmt->execute([
		'user' => $data['user'],
	]);

	if (!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	$usernameUsed = $stmt->rowCount() != 0;

	if ($usernameUsed) {
		throw new Exception(
			sprintf("Numele de utilizator %s este deja folosit. Te rog să alegi alt
			nume de utilizator <a href='utilizatori.php'>Înapoi</a>", $data['user']
			)
		);
	}

	$query = (
		"INSERT INTO `utilizator` (`tipOperator`,`nume`, `user`, `parola`, `tipCont`,`idFirma`)

		VALUES (
			:operatorType,
			:fullName,
			:username,
			:hasedPassword,
			:userType,
			:companyID
		)"
	);

	$stmt = $db->prepare($query);
	$ok    = $stmt->execute([
		'operatorType' => $data['tipOperator'],
		'fullName' => $data['nume'],
		'username' => $data['user'],
		'hasedPassword' => md5($data['parola']),
		'userType' => $data['tipCont'],
		'companyID' => $data['idFirma']
	]);

	if (!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	Page::showConfirmation(
		'<span class="confirmation">Utilizatorul a fost adăugat cu succes !</span>
		<a href="utilizatori.php ">Înapoi</a>'
	);
}
catch (Exception $e) {
	Page::showError($e->getMessage());
}
Page::showFooter();
?>
