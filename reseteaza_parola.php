<?php
	
	require_once "include/php/Utilizator.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	
	Page::showHeader();
	Page::showContent();
	
	
	$utilizator		= new Utilizator($_GET['id_user']);
	
		
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
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		
	
	Page::showConfirmation('<span class="confirmation">Parola a fost resetata cu succes ! Noua parola este </span><span style="font-size:18px;background:yellow;" class="bold">'.$parola_noua.'</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="utilizatori.php ">Înapoi</a>');
		
	
	Page::showFooter();