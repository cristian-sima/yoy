<?php

set_time_limit (0);

require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SituatieMecanica.php";
require_once "include/php/DataCalendaristica.php";
require_once "include/php/SituatieMecanicaTotaluri.php";

Page::showHeader();
Page::showContent();


Page::showHeading('<img src="img/setari.png" align="absmiddle" /> Actualizare situatii ', "");


if(isset($_POST['id_firma_do']) ){

$to = new DataCalendaristica($_POST['to']);

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


$firma = new FirmaSpatiu(addslashes($_POST['id_firma_do']));

$_data = new DataCalendaristica(addslashes($_POST['from']));
			
			
			
			do {
			
			
				//Page::representVisual($_data);
				
				$_OBJ_data = new DataCalendaristica($_data);
				
				// actualizare situatie
				
				// in felul asta se verifica daca avem situatie
				$situatie =  new SituatieMecanica($_OBJ_data, $_OBJ_data, $firma);
		
				$mysql	= "UPDATE `completare_mecanica` 
					SET 	`total_incasari` = '".$situatie->getTotalIncasari()."',
						 	`total_premii` = '".$situatie->getTotalPremii()."'
					WHERE `id` = '".$situatie->getId()."' ";	

				 mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
		
				console('Am actualizat situatia nr <b> '.$situatie->getId().'</b> din data de <b>'.$_OBJ_data.'</b> pentru firma <b>'.$firma->getDenumire().'</b>');
				
				$_data = SituatieMecanica::getUrmatoareaCompletareStrict($firma, $_OBJ_data);
				
			}while($_data && (strtotime($_data) <= (strtotime($to)) ));
			



console("<span style='weight:bold;background:green;color:white'> Totul este ok acum ! </span> <span style='color:red'>Va rugam sa actualizati totalul pentru luna respectiva.</span> <a href='http://localhost/beta/inchide_situatie_luna.php?an=".$_POST['an']."&luna=".$_POST['luna']."&id_firma=".$_POST['id_firma_do']."&data=".$_POST['from']."'>aici</a>");



}else {
	echo '
		Aceasta optiune se poate folosi dupa ce modificati date in situatiile din trecut (atat carnete de bilete cat și indexi). Se pot modifica și in baza de date situatiile
		
		<br/> 		
		<br />
		<form id="my_form" method="POST">
		De la inclusiv: <input type="text" id="from" name="from" class="datepicker" value="'.$_GET['data'].'" placeholder="Alegeti data"><br />
		Până la inclusiv: <input type="text" id="to" name="to" class="datepicker" value="'.$_GET['data'].'" placeholder="Alegeti data"><br />
		';
		echo'Pentru Firma:
			<input type="hidden" name="an" value="'.$_GET['an'].'" />
			<input type="hidden" name="luna" value="'.$_GET['luna'].'" />
			<select name="id_firma_do" id="firma">
			';

	
			$result = mysql_query("SELECT nume,id,activa
								FROM firma 
								ORDER BY activa DESC,nume ASC", 
			Aplicatie::getInstance()->getMYSQL()->getResource());

			while($firma = mysql_fetch_array($result))
			{
				echo'<option value="'.$firma['id'].'" '.(($firma['id'] == $_GET['id_firma'])?('selected'):"").'  '.(($firma['activa']=="0")?('style= "background:#FF5050" '):"").' >'.($firma['nume'])."</option>";
			}
			echo"</select>";
			
			$data = $_GET['data'];
		echo ' <br />
		<input type="submit" id="trimis" value="Actualizeaza (va rugam sa asteptati dupa ce dati click)!" /> 
		</form>
		<script>
		$(document).ready(function(){ 
		
			$("#from").datepicker(
			{


				changeMonth: true,
				numberOfMonths: 1,
				endDate: "",
				onClose: function (selectedDate)
				{
					$("#from").datepicker("option", "dateFormat", "yy-mm-dd");
				}
			});
		});
		$(document).ready(function(){ 
		
			$("#to").datepicker(
			{


				changeMonth: true,
				numberOfMonths: 1,
				endDate: "",
				onClose: function (selectedDate)
				{
					$("#to").datepicker("option", "dateFormat", "yy-mm-dd");
				}
			});
		});
		$("#trimis").click(function(){
			$(this).attr("disabled","disabled");
			$(this).val("Actualizez...");
			$("#my_form").submit();
		});
		</script>';
}

Page::showFooter();