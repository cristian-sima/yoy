<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";
require_once "app/FirmaSpatiu.php";
require_once "app/RegistruGraficCentral.php";
require_once "app/RegistruGraficFirma.php";

Design::showHeader();


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
	$result = mysql_query($mysql, Aplicatie::getInstance()->Database);

	$mysql = "INSERT INTO `sold_inchidere_luna`
					(`valoare`,
					`idFirma`,
					`data_`)
					 VALUES
					 ('".$registru->getTotal()."',
					 '".$data['id_firma']."',
					 '".$data['data']."'
					 )";
	$result = mysql_query($mysql, Aplicatie::getInstance()->Database);

	Design::showConfirmation('
		<big><span class="confirmation">Situația lunară a fost închisă</span></big>
		<a href="inchide_situatie_luna.php?data='.$data['data'].'">Înapoi la închideri</a>');
}
catch(Exception $e)
{
	Design::showError($e->getMessage());
}

Design::showFooter();
