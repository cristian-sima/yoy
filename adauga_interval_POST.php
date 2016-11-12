<?php

require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";

Page::showHeader();
Page::showContent();

try
{
	$data			=   $_POST;


	Procesare::checkRequestedData(array('from','to','tip','valoare'),$data,'adauga_interval_taxa?type='.$_POST['tip']);

	// content

	$isNow=0;

	if($data['to'] == "")
	{
		$data['to'] = DataCalendaristica::getZiuaUrmatoare(date("Y-m-d"));
		$isNow=1;
	}

	if(strtotime($data['from']) >= strtotime($data['to']))
	{
		throw new Exception("Prima dată trebuie să fie mai mare decât a doua !");
	}


	//introdu firma
	$q = "INSERT INTO `taxa`(`tip`, `_from`, `_to`, `valoare`,`isNow`) VALUES ('".$data['tip']."','".$data['from']."','".$data['to']."','".$data['valoare']."','".$isNow."')";
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL());


	// confirmation
	Page::showConfirmation('<span class="confirmation">Taxa a fost adaugată</span> <a href="setari.php">Înapoi</a>');

}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
