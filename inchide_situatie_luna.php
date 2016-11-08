<?php

require_once "include/php/Romanian.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/Procesare.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SelectSituatie_GUI.php";

Page::showHeader();
Page::showContent();


Procesare::createEmptyFields($_GET, array ('data', 'id_firma'));

$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
$total	 			= 0;
$data				= $selector_GUI->getDataCurenta();

$selector_GUI->afiseazaButon(true);
$selector_GUI->afiseazaFirme(false);
$selector_GUI->setAdresaButon("inchide_situatie_luna.php");
$selector_GUI->afiseazaDescriere(false);


Page::showHeading("Vizualizați inchiderile de situatii", '
			<input	class="disp" type="button" value="Înapoi actiuni" class="disp" onclick="document.location='."'".'actiuni.php?data='.$data."'".'" />
			');


$selector_GUI->display();

/* ---------------- content ---------------------*/


echo'&nbsp;&nbsp;&nbsp;&nbsp;<center><table id="example"><tr><th width="20%">DENUMIRE FIRMĂ</th><th width="30%">Actiune</th><th width="50%">Sold total</th></tr>';


$mysql = "
	SELECT 	f.nume,
			f.id,
			f.dataIncetare			
	FROM 	firma AS f	
	WHERE 	f.activa='1' OR ( f.dataIncetare>'".$data->getLastDayOfMonth()."')
	";
$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());

while($firma = mysql_fetch_array($result))
{
	$firma['valoare']	= 0;
	
	$mysql_2 = "
					SELECT 	count(idFirma) AS este, valoare
					FROM 	sold_inchidere_luna
					WHERE (idFirma='".$firma['id']."' AND data_>='".$data->getFirstDayOfMonth()."' AND data_<= '".$data->getLastDayOfMonth()."')
					";
	$result2 = mysql_query($mysql_2, Aplicatie::getInstance()->getMYSQL()->getResource()) or die(mysql_error());
	
	while($inchisa = mysql_fetch_array($result2))
	{
		$firma['inchisa'] = $inchisa['este'];
		$firma['valoare'] = $inchisa['valoare'];
	}

	echo'
	<tr>
		<td width="20%">'.$firma['nume'].'</td>
		<td width="30%"><input type="button" onclick="document.location=\'inchide_situatie_lunara_GET.php?id_firma='.$firma['id'].'&data='.$data->getLastDayOfMonth().'\'" value="Inchidere luna" />'.(($firma['dataIncetare'] != NULL)?"(incetata pe ".$firma['dataIncetare'].")":"").'</td>
		<td width="50%">'.(($firma['inchisa'] != 0)?Romanian::currency($firma['valoare']):"<span style='color:red'>Neinchis</span>").'</td>
	</tr>';
}

/* ------------------------- Firma Organizatoare ------------------------ */

$inchisa 	= 0;
$mysql 		= "
				SELECT 	count(idFirma) AS este,valoare
				FROM 	sold_inchidere_luna
				WHERE (idFirma='0' AND data_>='".$data->getFirstDayOfMonth()."' AND data_<= '".$data->getLastDayOfMonth()."')
				";
$result2 = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());

while($f2 = mysql_fetch_array($result2))
{
	$inchisa = $f2['este'];
	$valoare = $f2['valoare'];
}

echo'<tr></tr><tr></tr><tr></tr><tr><td class="bold" width="30%">'.Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire().'</td><td width="20%"><input type="button" onclick="document.location=\'inchide_situatie_lunara_GET.php?id_firma=0&data='.$data.'\'" value="Inchidere luna" /></td><td width="50%">'.(($inchisa != 0)?Romanian::currency($valoare):"<span style='color:red'>Neinchis</span>").'</td></tr></table>';

Page::showFooter();
?>


<style>

#example
{
	border-collapse: collapse;
	width:700px;
}

#example td
{
	padding:	5px;
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