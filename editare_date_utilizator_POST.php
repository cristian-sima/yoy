<?php

	require_once "app/Procesare.php";
	require_once "app/Aplicatie.php";
	require_once "app/Utilizator.php";

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

		$db = Aplicatie::getInstance()->Database;

		if(Aplicatie::getInstance()->getUtilizator()->isOperator())
		{
			$utilizator		= Aplicatie::getInstance()->getUtilizator();
		}
		else
		{
			$utilizator		= new Utilizator($db, $data['id_user']);
		}


		$data['user'] = str_replace('"', "", $data['user']);
		$data['nume'] = str_replace("'", "", $data['nume']);


		// content

		$r = mysql_query("SELECT user from utilizator WHERE user='".$data['user']."' AND id != '".$utilizator->getID()."'", $db);
		if(mysql_num_rows($r) != 0)
			throw new Exception ("Mai exista un utilizator cu acest username. Alegeti altul ! <a href='utilizatori.php'>Înapoi</a>");

		if(trim($data['parola']) == "")
			$q = "UPDATE utilizator SET idFirma='".$data['idFirma']."', nume='".$data['nume']."',user='".$data['user']."' WHERE id='".$utilizator->getID()."'";
		else
			$q = "UPDATE utilizator SET idFirma='".$data['idFirma']."', nume='".$data['nume']."',user='".$data['user']."', parola='".md5($data['parola'])."' WHERE id='".$utilizator->getID()."'";

		$result = mysql_query($q, $db);


		if(Aplicatie::getInstance()->getUtilizator()->isOperator() || $_SESSION['user'] == $data['user'])
		{
			//refresh session and cookies

			$_SESSION['user'] 		= $data['user'];
			$_SESSION['parola'] 	= md5($data['parola']);

			setcookie("cookuser", $_SESSION['user'], time()+60*60*24*100, "/");
			setcookie("cookpass", md5($data['parola']), time()+60*60*24*100, "/");
		}

		Design::showHeader();
		

		// confirmation
		if(Aplicatie::getInstance()->getUtilizator()->isOperator())
			Design::showConfirmation('<span class="confirmation">Modificarile au fost realizate.</span> <a href="index.php ">Înapoi</a>');
		else
			Design::showConfirmation('<span class="confirmation">Modificarile au fost realizate.</span> <a href="utilizatori.php ">Înapoi</a>');

	}
	catch(Exception $e)
	{
		Design::showHeader();
		

		Design::showError($e->getMessage());
	}

	Design::showFooter();
?>
