<?php

require_once "app/FirmaSpatiu.php";
require_once "app/Procesare.php";
require_once "app/Aplicatie.php";
require_once "app/Utilizator.php";

Login::permiteOperator();

Page::showHeader();
Page::showContent();

try
{
	$firma = new FirmaSpatiu($_POST['id_firma']);

	$data			=   $_POST;

	$q = "UPDATE  `firma` SET `nume` = '".$data['nume']."', `localitate`='".$data['localitate']."', `dateContact`='".$data['dateContact']."', `comentarii`='".$data['comentarii']."'  WHERE id = '".$data['id_firma']."' ";
	$result = mysql_query($q, Aplicatie::getInstance()->Database);

	Page::showConfirmation('<span class="confirmation">Datele au fost modificate</span> <a href="details.php?idFirma='.$data['id_firma'].'">Înapoi</a>');


}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
?>