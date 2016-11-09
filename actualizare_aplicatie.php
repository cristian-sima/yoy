<?php

require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SituatieMecanica.php";
require_once "include/php/DataCalendaristica.php";
require_once "include/php/SituatieMecanicaTotaluri.php";

Page::showHeader();
Page::showContent();


Page::showHeading('<img src="img/setari.png" align="absmiddle" /> Update application to version 2.8 ... ', "");


function console($message)
{
	echo "<li><pre>".$message.'</pre></li><br />';
}


function stop($m)
{	
	console("A aparut o eroare");
	console($m);
	die();	
}


console("Actualizarea aplicatiei. ");
console("Operatiunea poate dura cateva minute. Va rugam sa nu inchideti fereastrea din browser");

console("Prelucrez baza de date");
console("Schimb structura bazei de date... ");

mysql_query("ALTER TABLE `completare_mecanica` ADD `total_premii` FLOAT NOT NULL , ADD `total_incasari` FLOAT NOT NULL ;") or stop(mysql_error());

console("Structura schimbata cu succes");

console("Incep procesarea datelor...");



$sql = "SELECT * FROM `completare_mecanica`";
$result = mysql_query($sql) or die(musql_error());


while($situatie = mysql_fetch_array($result))
{	
	//  ACTUALIZEZ SITUATIE
	
	$data_situatie = new DataCalendaristica($situatie['data_']);
	$firma = new FirmaSpatiu($situatie['id_firma']);
		
		// in felul asta se verifica daca avem situatie
	$situatie_rezultata =  new SituatieMecanica($data_situatie, $data_situatie, $firma);
		
	$mysql	= "UPDATE `completare_mecanica` 
					SET 	`total_incasari` = '".$situatie_rezultata->getTotalIncasari()."',
						 	`total_premii` = '".$situatie_rezultata->getTotalPremii()."'
					WHERE `id` = '".$situatie['id']."' ";	

	mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
	
	console('Am actualizat situatia nr <b> '.$situatie['id'].'</b> din data de <b>'.$data_situatie.'</b> pentru firma <b>'.$firma->getDenumire().'</b>');
}

console("<span style='weight:bold;background:green;color:white'> Totul este ok acum ! Puteti folosi aplicatia. Click <a href='index.php'>aici</a>");






Page::showFooter();