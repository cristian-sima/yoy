<?php
	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/SituatieMecanica.php";
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
		Procesare::checkRequestedData(array('aparate_','from','id_firma'),$data,'situatie_mecanica.php?id='.$_POST['id_firma']);
		$firma			= new FirmaSpatiu($data['id_firma']);
		$autor			= Aplicatie::getInstance()->getUtilizator();
		$aparate_		= explode("|", $data['aparate_']);
		$id_old_completare_mecanica		= 0;
		// Page::representVisual($data);

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
		// sterge orice index-uri de  din aceasta zi și pentru aceasta firma
		$mysql	= "DELETE FROM `index_mecanic` WHERE `id_completare` = '".$id_old_completare_mecanica."' ";
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		// introducere
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
		$situatie =  new SituatieMecanica($data_situatie, $data_situatie, $firma);
		$mysql	= "UPDATE `completare_mecanica`
					SET 	`total_incasari` = '".$situatie->getTotalIncasari()."'
					WHERE `id` = '".$id_completare_mecanica."' ";
		mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		Page::showConfirmation('<span class="confirmation"> Situația pe data de '.$data['from'].' a fost modificată !</span> <span style="color:orange" class="bold"> Vă rugăm să tipăriți situația acum !</span>  <a href="'.((Aplicatie::getInstance()->getUtilizator()->isOperator())?("situatie_mecanica_operator.php"):("situatie_mecanica.php")).'?id_firma='.$data['id_firma'].'&from='.$data['from'].'&to='.$data['from'].'">Înapoi la situație</a>');
	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}
	Page::showFooter();
