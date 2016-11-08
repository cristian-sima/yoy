<?php

require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/RegistruGraficCentral.php";
require_once "include/php/RegistruGraficFirma.php";

Page::showHeader();
Page::showContent();

try
{
	$data			=   $_GET;

	Procesare::checkRequestedData(array('id_firma','data'), $data, 'inchide_situatie_luna.php');

	$data_dorita		= new DataCalendaristica($data['data']);
	
	if($data['id_firma'] == "0")
	{
		$registru = new RegistruGraficCentral($data_dorita);
	}
	else
	{
		$firma				= new FirmaSpatiu($data['id_firma']);		
		$registru = new RegistruGraficFirma($firma, $data_dorita);
	}

	$mysql = "DELETE FROM `sold_inchidere_luna` WHERE idFirma = '".$data['id_firma']."' AND data_>='".$data_dorita->getFirstDayOfMonth()."' AND data_<='".$data_dorita->getLastDayOfMonth()."'";
	$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());

	$mysql = "INSERT INTO `sold_inchidere_luna`
					(`valoare`, 
					`idFirma`, 
					`data_`)
					 VALUES 
					 ('".$registru->getTotal()."',
					 '".$data['id_firma']."',
					 '".$data['data']."'
					 )";
	$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());

	Page::showConfirmation('
		<big><span class="confirmation">Situatia lunara a fost inchisa</span></big>
		<a href="inchide_situatie_luna.php?data='.$data['data'].'">Înapoi la inchideri</a>');
}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();