<?php

require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";

Page::showHeader();
Page::showContent();

try
{
	$data			=   $_GET;


	Procesare::checkRequestedData(array('id'), $data,'setari.php');


	$q = "DELETE FROM taxa WHERE id='".$data['id']."' ";
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL());


	// confirmation
	Page::showConfirmation('<span class="confirmation">Perioada a fost ștearsă</span> <a href="setari.php">Înapoi la setări</a>');
	
}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();