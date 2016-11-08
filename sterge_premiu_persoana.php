<?php

require_once "include/php/FirmaSpatiu.php";
require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Utilizator.php";
require_once "include/php/Guvern.php";

Page::showHeader();
Page::showContent();

try
{
	$data 						= $_GET;

	//Page::representVisual($data);

	Procesare::checkRequestedData(  array('id_premiu','id_firma'),$data,'acorda_premii.php');
	
	$q = "DELETE FROM impozit WHERE  id='".$data['id_premiu']."' ";
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());

	Page::showConfirmation('<span class="confirmation">Persoana a fost stearsa cu succes !</span> <a href="acorda_premii.php?id_firma='.$data['id_firma'].' ">Inapoi acordare premii</a>');
}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
?>		