<?php
	
	require_once "app/Procesare.php";
	require_once "app/Aplicatie.php";
	require_once "app/Utilizator.php";
	
	Design::showHeader();
		
	
	try 
	{	
		$data			=   $_POST;
		
		Procesare::checkRequestedData(array('nume','patron','localitate'),$data,'setari.php');	

		$q = "UPDATE  `firma_organizatoare` SET `nume` = '".$data['nume']."', `localitate`='".$data['localitate']."', `patron`='".$data['patron']."' WHERE id='1'";
		$result = mysql_query($q, Aplicatie::getInstance()->Database);
	
		Design::showConfirmation('<span class="confirmation">Datele au fost modificate</span> <a href="setari.php ">Înapoi</a>');

	
	}
	catch(Exception $e)
	{
		Design::showError($e->getMessage());
	}				
	
	Design::showFooter();	
?>			