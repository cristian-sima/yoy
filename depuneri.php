<?php

require_once "include/php/Guvern.php";
require_once "include/php/Romanian.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SelectSituatie_GUI.php";

Page::showHeader();
Page::showContent();


Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));

$_criterii_MYSQL	= (($_GET['id_firma'] != '')?("AND i.idFirma='".$_GET['id_firma']."'"):(""));
$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
$total	 			= 0;
$data				= $selector_GUI->getDataCurenta();

$selector_GUI->afiseazaButon(true);
$selector_GUI->afiseazaFirme(false);
$selector_GUI->setAdresaButon("depuneri.php");
$selector_GUI->setTypeOfDocument("Depuneri de numerar");


Page::showHeading("Vizualizați depuneri de numerar", '
			<input class="disp" type="button" value="Tipărește" class="disp" onclick="window.print();" />
			<input	class="disp" type="button" value="Înapoi acțiuni" class="disp" onclick="document.location='."'".'actiuni.php?data='.$data."'".'" />
			<input	class="disp" type="button" value="Adaugă depunere" class="disp" onclick="document.location='."'".'depunere_numerar_noua.php?data='.$data."'".'" />

			');


$selector_GUI->display();

/* ---------------- content ---------------------*/

$mysql = "SELECT 	data,
				suma,
				document,
				explicatie
		FROM depunere_numerar
		WHERE data >= '".$data->getFirstDayOfMonth()."' AND data <= '".$data->getLastDayOfMonth()."'";

$eliberari 		= mysql_query($mysql, Aplicatie::getInstance()->Database);
$nr_crt			= 1;

if(mysql_num_rows($eliberari) == 0)
{
	echo 'Nu sunt depuneri de numerar disponibile ';
}
else
{
	echo '<table id="example" style="margin-top:20px;width:100%">
					<tr class="head pad">
					<td> Nr. crt</td>
					<td> Data</td>
					<td> Valoare</td>
					<td> Document</td>
					<td> Explicație</td>
					</tr>
					';

	while($depunere = mysql_fetch_array($eliberari))
	{
		echo'
			<tr class="pad">
				<td> '.$nr_crt++.'</td>
				<td> '.$depunere['data'].'</td>
				<td> '.Romanian::currency($depunere['suma']).'</td>
				<td> '.htmlspecialchars($depunere['document']).'</td>
				<td> '.htmlspecialchars($depunere['explicatie']).'</td>
			</tr>
			';
		$total += $depunere['suma'];
	}

	echo'
			<tr class="pad">
				<td class="bold" > Total</td>
				<td> </td>
				<td class="bold">'.Romanian::currency($total).'</td>
				<td></td>
				<td></td>
			</tr>';

	echo '</table>';
}



/* ---------------- END of content ---------------------*/



Page::showFooter();
?>

<style>

#example
{
	border-collapse: collapse;
}

#example td
{
	border:		1px solid #dfdfdf;
}

#example tr:hover {
	background: rgb(255, 230, 184);
}

@page {
	size: portrait
}

.pad td {
	padding: 5px;
}

.head td {
	background: rgb(253, 241, 240);
	font-weight: bold;
}

</style>
