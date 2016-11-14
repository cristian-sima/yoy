<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Design::showHeader();


try
{
	$data			=   $_GET;


	Procesare::checkRequestedData(array('id'), $data,'setari.php');


	$q = "DELETE FROM taxa WHERE id='".$data['id']."' ";
	$result = mysql_query($q, Aplicatie::getInstance()->Database);


	// confirmation
	Design::showConfirmation('<span class="confirmation">Perioada a fost ștearsă</span> <a href="setari.php">Înapoi la setări</a>');
	
}
catch(Exception $e)
{
	Design::showError($e->getMessage());
}

Design::showFooter();