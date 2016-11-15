<?php

require_once "app/FirmaSpatiu.php";
require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Design::showHeader();


try
{
	$data			=   $_GET;
	$firma			= new FirmaSpatiu($_GET['id_firma']);

	Procesare::checkRequestedData(
	array('id_firma'),
	$data,
							'editare_date_firma.php?id_firma='.$_GET['id_firma']);

	$q = "UPDATE `firma` SET activa='0',dataIncetare='".date("Y-m-d")."' WHERE id='".$data['id_firma']."'";
	$result = mysql_query($q, Aplicatie::getInstance()->Database);

	$q = "UPDATE `utilizator` SET activ='1' WHERE idFirma='".$data['id_firma']."'";
	$result = mysql_query($q, Aplicatie::getInstance()->Database);


	Design::showConfirmation('<span class="confirmation">Contractul a fost încheiat</span> <a href="company_details.php?id='.$_GET['id_firma'].'">Înapoi</a>');


}
catch(Exception $e)
{
	Design::showError($e->getMessage());
}

Design::showFooter();
?>
