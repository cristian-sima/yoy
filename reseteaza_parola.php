<?php

	require_once "app/Utilizator.php";
	require_once "app/Aplicatie.php";
	require_once "app/FirmaSpatiu.php";

	Design::showHeader();
	

	$db = Aplicatie::getInstance()->Database;

	$utilizator		= new Utilizator($db, $_GET['id_user']);


	function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
	{
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
		$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
		$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
		$sets[] = '!@#$%_*?';

		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}

		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
		$password .= $all[array_rand($all)];

		$password = str_shuffle($password);

		if(!$add_dashes)
		return $password;

		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}


	$parola_noua = generateStrongPassword(10);


	//introdu firma
	$q = "UPDATE  `utilizator`
			SET `parola` = '".md5($parola_noua)."'
			WHERE id='".$utilizator->getID()."'";
	$result = mysql_query($q, $db);


	Design::showConfirmation('<span class="confirmation">Parola a fost resetată ! Noua parolă este </span><mark>'.$parola_noua.'</mark>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="utilizatori.php ">Înapoi</a>');


	Design::showFooter();
