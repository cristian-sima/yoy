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
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());


	// confirmation
	Page::showConfirmation('<span class="confirmation">Perioada a fost stearsa</span> <a href="setari.php">Înapoi la setari</a>');
	
}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();