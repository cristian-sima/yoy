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


		$r = mysql_query("SELECT user from utilizator WHERE user='".$data['user']."'", Aplicatie::getInstance()->getMYSQL()->getResource());
		if(mysql_num_rows($r) != 0)
			throw new Exception ("Mai exista un utilizator cu acest username. Alegeti altul ! <a href='utilizatori.php'>Înapoi</a>");

		$q = "INSERT INTO `utilizator`(`tipOperator`,`nume`, `user`, `parola`, `tipCont`,`idFirma`) VALUES ('".$data['tipOperator']."','".$data['nume']."','".$data['user']."','".md5($data['parola'])."','".$data['tipCont']."','".$data['idFirma']."')";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());


		Page::showConfirmation('<span class="confirmation">Utilizatorul a fost adăugat cu succes !</span> <a href="utilizatori.php ">Înapoi</a>');	

	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}

	Page::showFooter();
?>
