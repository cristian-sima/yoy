<?php

require_once "include/php/Aparat.php";
require_once 'include/php/Aplicatie.php';
require_once 'include/php/FirmaSpatiu.php';
require_once "include/php/SelectSituație_GUI.php";

Page::showHeader();
Page::showContent();


Page::showHeading("Optiuni aparat", "");

$aparat		= new Aparat($_GET['id_aparat']);



$result = mysql_query("SELECT nume,id,activa
									FROM firma 
									WHERE id != '".$aparat->getFirmaCurenta()."' AND activa = '1'
									ORDER BY activa DESC,nume ASC", Aplicatie::getInstance()->getMYSQL()->getResource());
	




$q="SELECT * FROM istoric_aparat WHERE id_aparat='".$aparat->getID()."' ORDER BY id";
$result2 = mysql_query($q,Aplicatie::getInstance()->getMYSQL()->getResource());

echo '<link href="include/css/fieldset.css" rel="stylesheet" type="text/css"/>
		<fieldset>
		<legend>Istoric aparat</legend>
		<table width="100%">
			<tr>
				<td class="smoke"> Unitatea</td>
				<td class="smoke"> De la</td>
				<td class="smoke"> Pana la</td>
			</tr>';

while($istoric_aparat = mysql_fetch_array($result2))
{
	if($istoric_aparat['id_firma'] == '0')
	{
		$nume_firma 	= "Depozit";
	}
	else
	{
		$firma2			= new FirmaSpatiu($istoric_aparat['id_firma']);
		$nume_firma		= $firma2->getDenumire();
	}
	
	echo '	<tr>
				<td class="smoke" style="background: rgb(231, 231, 231);">'.$nume_firma.'</td>
				<td class="smoke" style="background: rgb(231, 231, 231);">'.$istoric_aparat['from_'].'</td>
				<td style="background: rgb(231, 231, 231);" class="smoke" style="background: rgb(231, 231, 231);">'.(($istoric_aparat['is_now'] == '1')?("In prezent"):($istoric_aparat['to_'])).'</td>
			</tr>';
}

echo'</table></fieldset>';




echo  '<table width="100%">
		<tr>
			<td style="width:50%">
				<fieldset>
					<legend>Editeaza date aparat</legend>
					<form action="editeaza_date_aparat.php">
						<input type="hidden" value="'.$aparat->getID().'" name="id_aparat" />
						<input  type="submit" value="Editeaza date aparat" />
					</form>
					<br />
				</fieldset>
			</td>
			<td style="width:50%">
				<fieldset>
					<legend> Mutati aparat in depozit </legend>
					<form action="muta_aparat_in_depozit.php">
					<br />
					<span title="Mutarea aparatului nu influenteaza situatia de astazi, din firma respectiva. Aceasta situatie poate sa fie completata pana la ora 24 astazi. De maine, aparatul nu mai apare  in firma " style="color:blue;text-decoration:underline">Ce inseamna mutarea in depozit ?</span><br /><br />
						<input type="hidden" value="'.$aparat->getID().'" name="id_aparat" />						
						<input '.(($aparat->isActiv())?(""):("disabled")).' '.(($aparat->isInDepozit())?("disabled"):("")).' type="submit" value="Mutati aparat in depozit" />
					</form>
					<br />
				</fieldset>
			</td>
		</tr>
		<tr>
			<td style="width:50%">
				<fieldset>
					<legend> Scoati aparat din uz </legend>
					<br />
					<span title="Eliminarea aparatului nu influenteaza situatia de astazi, din firma respectiva. Aceasta situatie poate sa fie completata pana la ora 24 astazi. De maine, aparatul nu mai apare  in firma " style="color:blue;text-decoration:underline">Ce inseamna eliminarea ?</span><br />
					<br />
					<form action="scoate_aparat_din_uz.php">
						<input type="hidden" value="'.$aparat->getID().'" name="id_aparat" />
						<input '.(($aparat->isActiv())?(""):("disabled")).' type="submit" value="Scoateti aparat din uz" />
					</form> 
					<br />
				</fieldset>
			</td>
			<td style="width:50%">';
			

			if(mysql_num_rows($result) === 0)
			{
				echo "Nu exista alte firme active la care sa fie mutat";
			}
			else 
			{
			echo '
				<form action="muta_aparat_la_firma.php" method="POST">
					<fieldset>
						<legend>Muta aparat</legend>
						<br />
						<span title="Mutarea aparatului nu influenteaza situatia de astazi de la firma veche. Aceasta situatie poate sa fie completata pana la ora 24 astazi. De maine, aparatul nu mai apare  in firma. Insa, pentru Firmă nouă, se va crea o noua situatie (sau se modifica cea actuala) si se adauga aparatul" style="color:blue;text-decoration:underline">Ce inseamna mutarea intre 2 firme ?</span><br />
						<br />
						<input type="hidden" value="'.$aparat->getID().'" name="id_aparat" />						
						<input type="hidden" name="id_aparat" value="'.$aparat->getID().'" />
						<input type="text"  name="mecanic_intrare" placeholder="Index intrari" />
						<input type="text"  name="mecanic_iesire" placeholder="Index iesiri" /><br />
							<select name="id_firma_noua">';
							while($firma = mysql_fetch_array($result))
							{
								echo'<option value="'.$firma['id'].'"  >'.($firma['nume'])."</option>";
							}
						echo' 
						</select>
						<input '.(($aparat->isActiv())?(""):("disabled")).' type="submit" value="Mutati aparat la alta firma" />
					</fieldset>
				</form>';
			}
			echo '
			</td>
		</tr>
	</table>
	<script>
	$(function ()
	{
		$(document).tooltip();
	});
	$(document).tooltip(
	{
		position:
		{
			my: "center bottom-20",
			at: "center top",
			using: function (position, feedback)
			{
				$(this).css(position);
				$("<div>")
					.addClass("arrow")
					.addClass(feedback.vertical)
					.addClass(feedback.horizontal)
					.appendTo(this);
			}
		}
	});
	</script>';
		
		
Page::showFooter();
