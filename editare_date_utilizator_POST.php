<?php

	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/Utilizator.php";

	Login::permiteOperator();


	// for sql_injection for POST
	foreach ($_POST as $index => $value)
	{
		$_POST[$index] 	= mysql_real_escape_string($value);
	}

	// for sql_injection for GET
	foreach ($_GET as $index => $value)
	{
		$_GET[$index] 	= mysql_real_escape_string($value);
	}


	try
	{
		$data			=   $_POST;


		Procesare::checkRequestedData(array('user','nume','parola','idFirma', 'id_user'),$data,'editare_date_utilizator.php?id_user='.$_POST['id_user']);


		if(Aplicatie::getInstance()->getUtilizator()->isOperator())
		{
			$utilizator		= Aplicatie::getInstance()->getUtilizator();
		}
		else
		{
			$utilizator		= new Utilizator($data['id_user']);
		}


		$data['user'] = str_replace('"', "", $data['user']);
		$data['nume'] = str_replace("'", "", $data['nume']);


		// content

		$q1 = "SELECT user from utilizator WHERE user='".$data['user']."' AND id != '".$utilizator->getID()."'";

		$safeQuery = mysql_real_escape_string($q1);

		$r = mysql_query($safeQuery, Aplicatie::getInstance()->getMYSQL()->getResource());
		if(mysql_num_rows($r) != 0) {
			throw new Exception ("Mai exista un utilizator cu acest username. Alegeti altul ! <a href='utilizatori.php'>Înapoi</a>");
		}

		if(trim($data['parola']) == "")
			$q = "UPDATE utilizator SET idFirma='".$data['idFirma']."', nume='".$data['nume']."',user='".$data['user']."' WHERE id='".$utilizator->getID()."'";
		else
			$q = "UPDATE utilizator SET idFirma='".$data['idFirma']."', nume='".$data['nume']."',user='".$data['user']."', parola='".md5($data['parola'])."' WHERE id='".$utilizator->getID()."'";


		$safeQuery = mysql_real_escape_string($q);


		$result = mysql_query($safeQuery, Aplicatie::getInstance()->getMYSQL()->getResource());


		if(Aplicatie::getInstance()->getUtilizator()->isOperator() || $_SESSION['user'] == $data['user'])
		{
			//refresh session and cookies

			$_SESSION['user'] 		= $data['user'];
			$_SESSION['parola'] 	= md5($data['parola']);

			setcookie("cookuser", $_SESSION['user'], time()+60*60*24*100, "/");
			setcookie("cookpass", md5($data['parola']), time()+60*60*24*100, "/");
		}

		Page::showHeader();
		Page::showContent();

		// confirmation
		if(Aplicatie::getInstance()->getUtilizator()->isOperator())
			Page::showConfirmation('<span class="confirmation">Modificarile au fost realizate.</span> <a href="index.php ">Înapoi</a>');
		else
			Page::showConfirmation('<span class="confirmation">Modificarile au fost realizate.</span> <a href="utilizatori.php ">Înapoi</a>');

	}
	catch(Exception $e)
	{
		Page::showHeader();
		Page::showContent();

		Page::showError($e->getMessage());
	}

	Page::showFooter();
?>
