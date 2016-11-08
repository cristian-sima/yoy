<?php

require_once "include/php/Procesare.php";
require_once "include/php/Aplicatie.php";

Page::showHeader();
Page::showContent();

try
{
	$data			=   $_POST;



	Procesare::checkRequestedData(array('data','suma', 'document','explicatie'),$data,'administreaza_deconturi.php');

	
	$mysql = "INSERT INTO `decont`
					(`explicatie`,
					`data`,
					`suma`,
					`document`) 
					
					VALUES 
					('".$data['explicatie']."',
					'".$data['data']."',
					'".$data['suma']."',
					'".$data['document']."')";

	$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
				

	Page::showConfirmation('<span class="confirmation">Decontul a fost realizat cu succes !</span> <a href="administreaza_deconturi.php ">Inapoi la deconturi</a>');

}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();
?>