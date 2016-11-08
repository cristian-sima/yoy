<?php
	
	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/SituațieMecanica.php";
	
	Login::permiteOperator();
	
	Page::showHeader();
	Page::showContent();	
	
	
	// for sql_injection for POST
	foreach ($_POST as $index => $value)
	{
		$_POST[$index] 	= mysql_real_escape_string($value);
	}
	
	// for sql_injection for GET
	foreach ($_GET as $index => $value)
	{
		$_GET[$index] 	= mysql_real_escape_string($value);
	}
	
	
	try 
	{	
		$data			=   $_POST;
		Procesare::checkRequestedData(array('aparate_','carnete_','from','id_firma'),$data,'situatie_mecanica.php?id='.$_POST['id_firma']);	
		
	
		
		$firma			= new FirmaSpatiu($data['id_firma']);
		$autor			= Aplicatie::getInstance()->getUtilizator();
		
		$carnete_		= explode("|", $data['carnete_']);
		$aparate_		= explode("|", $data['aparate_']);
		
		$id_old_completare_bilete		= 0;
		$id_old_completare_mecanica		= 0;

		
		// Page::representVisual($data);
			
		
		/*
		 * ------------------------------------------------------------------------
		 * 
		 * 								Completare bilete
		 * 
		 * ------------------------------------------------------------------------
		 */
		
		
		// sterge orice completare de bilete existenta pentru aceasta firma la aceasta data		
		$q = "SELECT id FROM completare_bilete WHERE data_ = '".$data['from']."' AND id_firma = '".$firma->getID()."' LIMIT 1";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		while($completare = mysql_fetch_array($result))
		{
			$id_old_completare_bilete	= $completare['id'];
		}	
		
		$mysql	= "DELETE FROM `completare_bilete` WHERE id='".$id_old_completare_bilete."' ";		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		
		// creare o noua completare bilete						
		$mysql	= "INSERT INTO `completare_bilete`(`id_firma`,`data_`) VALUES ('".$firma->getID()."', '".$data['from']."')";		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		$q = "SELECT id FROM completare_bilete WHERE data_ = '".$data['from']."' AND id_firma = '".$firma->getID()."' LIMIT 1";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		while($completare = mysql_fetch_array($result))
		{
			$id_completare_bilete	= $completare['id'];
		}		
		
		
		// sterge orice index-uri de bilete din aceasta zi si pentru aceasta firma	
		$mysql	= "DELETE FROM `carnete_bilete` WHERE `id_completare` = '".$id_old_completare_bilete."' ";		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		
		// introducere bilete				
		$mysql	= "INSERT INTO `carnete_bilete`(`start`,`end`,`id_completare`) 
					VALUES ('".$data['carnet_default_start']."','".$data['carnet_default_end']."', '".$id_completare_bilete."'),";
		if(count($carnete_) != 0)
		{
			foreach ($carnete_ as $carnet) 
			{
				if($carnet != '')
					$mysql .= "('".$data['carnet_'.$carnet.'_start']."','".$data['carnet_'.$carnet.'_end']."','".$id_completare_bilete."' ),";
			}
		}		
		$mysql = rtrim($mysql, ",").';';	
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		/*
		 * ------------------------------------------------------------------------
		 * 
		 * 								Completare mecanica
		 * 
		 * ------------------------------------------------------------------------
		 */
		
		
		
		// sterge orice completare de  index-uri existente pentru aceasta firma la aceasta data		
		$q = "SELECT id FROM completare_mecanica WHERE data_ = '".$data['from']."' AND id_firma = '".$firma->getID()."' LIMIT 1";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		while($completare = mysql_fetch_array($result))
		{
			$id_old_completare_mecanica	= $completare['id'];
		}	
		
		$mysql	= "DELETE FROM `completare_mecanica` WHERE id='".$id_old_completare_mecanica."' ";		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		
		// creare o noua completare mecanica					
		$mysql	= "INSERT INTO `completare_mecanica`(`id_firma`,`data_`,`autor`) VALUES ('".$firma->getID()."', '".$data['from']."', '".$autor->getID()."')";		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		$q = "SELECT id FROM completare_mecanica WHERE data_ = '".$data['from']."' AND id_firma = '".$firma->getID()."' LIMIT 1";
		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
		while($completare = mysql_fetch_array($result))
		{
			$id_completare_mecanica	= $completare['id'];
		}		
		
		
		// sterge orice index-uri de bilete din aceasta zi si pentru aceasta firma	
		$mysql	= "DELETE FROM `index_mecanic` WHERE `id_completare` = '".$id_old_completare_mecanica."' ";		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		
		// introducere bilete				
		$mysql	= "INSERT INTO `index_mecanic`(
											`id_aparat`, 
											`id_completare`,
											`start_intrari`,
											`end_intrari`,
											`start_iesiri`,
											`end_iesiri`
											) VALUES";
		foreach ($aparate_ as $aparat) 
		{
			$mysql .= "(
							'".$aparat."',
							'".$id_completare_mecanica."',
							'".$data['aparat_'.$aparat.'_start_intrari']."',
							'".$data['aparat_'.$aparat.'_end_intrari']."',
							'".$data['aparat_'.$aparat.'_start_iesiri']."',
							'".$data['aparat_'.$aparat.'_end_iesiri']."'
							),";
		}		
		$mysql = rtrim($mysql, ",").';';	
		
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		/*
		 * 
		 *	 MODIFICARE TOTALURI SITUATIE MECANICA folosind indexurile din baza de date
		 *
		 *
		 *
		 */
		
		$data_situatie = new DataCalendaristica($data['from']);
		
		// in felul asta se verifica daca avem situatie
		$situatie =  new SituațieMecanica($data_situatie, $data_situatie, $firma);
		
		$mysql	= "UPDATE `completare_mecanica` 
					SET 	`total_incasari` = '".$situatie->getTotalIncasari()."',
						 	`total_premii` = '".$situatie->getTotalPremii()."'
					WHERE `id` = '".$id_completare_mecanica."' ";	

		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
		
		
		
		
		
		Page::showConfirmation('<span class="confirmation"> Situatia pe data de '.$data['from'].' a fost modificata !</span> <span style="color:orange" class="bold"> Va rugam sa printati situatia acum !</span>  <a href="'.((Aplicatie::getInstance()->getUtilizator()->isOperator())?("situatie_mecanica_operator.php"):("situatie_mecanica.php")).'?id_firma='.$data['id_firma'].'&from='.$data['from'].'&to='.$data['from'].'">Înapoi la situatie</a>');
	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}				
	
	Page::showFooter();	