<?php

	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";

	Page::showHeader();
	Page::showContent();

	try
	{
		$data			=   $_POST;


		Procesare::checkRequestedData(
							array('tipOperator','nume','user','parola','idFirma','tipCont'),
							$data,
							'utilizatori.php');

    $query1 = "SELECT user from utilizator WHERE user='".$data['user']."'";

		$safeQuery = mysql_real_escape_string($query1);

		$r = mysql_query($safeQuery, Aplicatie::getInstance()->getMYSQL()->getResource());
		if(mysql_num_rows($r) != 0) {
			throw new Exception ("Mai exista un utilizator cu acest username. Alegeti altul ! <a href='utilizatori.php'>Înapoi</a>");
		}

		$query2 = "INSERT INTO `utilizator`(`tipOperator`,`nume`, `user`, `parola`, `tipCont`,`idFirma`) VALUES ('".$data['tipOperator']."','".$data['nume']."','".$data['user']."','".md5($data['parola'])."','".$data['tipCont']."','".$data['idFirma']."')";

		$safeQuery = mysql_real_escape_string($query2);

		$result = mysql_query($safeQuery, Aplicatie::getInstance()->getMYSQL()->getResource());

		Page::showConfirmation('<span class="confirmation">Utilizatorul a fost adăugat cu succes !</span> <a href="utilizatori.php ">Înapoi</a>');

	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}

	Page::showFooter();
?>
