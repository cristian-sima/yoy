<?php
	
	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	
	Page::showHeader();
	Page::showContent();	
	
	try 
	{	
		$data			=   $_GET;
		
		Procesare::checkRequestedData(
							array('id_user','type'),
							$data,
							'utilizatori.php');	
							
		$utilizator		= new Utilizator($data['id_user']);
	
		$q = "UPDATE  utilizator  SET activ='".$data['type']."' WHERE id='".$utilizator->getID()."' ";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			
		
		Page::showConfirmation('<span class="confirmation">Datele au fost modificate</span> <a href="utilizatori.php ">Inapoi la utilizatori</a>');
		
	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}				
	
	Page::showFooter();	
?>