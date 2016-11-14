<?php

require_once "app/Aparat.php";
require_once "app/Aplicatie.php";


Design::showHeader();


try
{
	$data			=   $_GET;
	$aparat			= new Aparat($data['id_aparat']);

	if(!$aparat->isActiv())
	{
		Design::complain("Aparatul a fost deja scos din uz !");
	}
	else
	{
		$today		= new DataCalendaristica(date("Y-m-d"));

		$q = "UPDATE  `istoric_aparat` SET `is_now`	= '0',
											`to_`	= '".$today."'
											 WHERE id_aparat = '".$data['id_aparat']."' AND is_now ='1' ";
		
		$result = mysql_query($q, Aplicatie::getInstance()->Database) or die(mysql_error());
		
		
		
		$q = "UPDATE  `aparat` SET `in_depozit`	= '1' WHERE id = '".$data['id_aparat']."'  ";
		
		
			
		$result = mysql_query($q, Aplicatie::getInstance()->Database) or die(mysql_error());
		
		
		
		$query = "INSERT INTO `istoric_aparat`
							(							
								`from_`,
								`id_aparat`,
								`id_firma`,
								`is_now`
							) 
							VALUES (
								'".$today."',
								'".$aparat->getID()."',
								'0',
								'1'
							)";
	
		
		$result = mysql_query($query, Aplicatie::getInstance()->Database);
		
	}


	Design::showConfirmation('<span class="confirmation">Aparatul a fost mutat în depozit</span> <a href="optiuni_aparat.php?id_aparat='.$data['id_aparat'].'">Înapoi la optiuni aparat</a>');

}
catch(Exception $e)
{
	Design::showError($e->getMessage());
}

Design::showFooter();