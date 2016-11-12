<?php
	
	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/Utilizator.php";
	
	Page::showHeader();
	Page::showContent();	
	
	try 
	{	
		$data			=   $_POST;
		
		Procesare::checkRequestedData(array('nume','patron','localitate'),$data,'setari.php');	

		$q = "UPDATE  `firma_organizatoare` SET `nume` = '".$data['nume']."', `localitate`='".$data['localitate']."', `patron`='".$data['patron']."' WHERE id='1'";
		$result = mysql_query($q, Aplicatie::getInstance()->Database);
	
		Page::showConfirmation('<span class="confirmation">Datele au fost modificate</span> <a href="setari.php ">Înapoi</a>');

	
	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}				
	
	Page::showFooter();	
?>			