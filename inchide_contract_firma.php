<?php

require_once "include/php/FirmaSpatiu.php";
require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";

Page::showHeader();
Page::showContent();

try
{
	$data			=   $_GET;
	$firma			= new FirmaSpatiu($_GET['id_firma']);

	Procesare::checkRequestedData(
	array('id_firma'),
	$data,
							'editare_date_firma.php?id_firma='.$_GET['id_firma']);

	$q = "UPDATE `firma` SET activa='0',dataIncetare='".date("Y-m-d")."' WHERE id='".$data['id_firma']."'";
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());

	$q = "UPDATE `utilizator` SET activ='1' WHERE idFirma='".$data['id_firma']."'";
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());


	Page::showConfirmation('<span class="confirmation">Contractul a fost încheiat</span> <a href="details.php?idFirma='.$_GET['id_firma'].'">Înapoi</a>');


}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
?>
