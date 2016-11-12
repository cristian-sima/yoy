<?php

require_once "include/php/Aplicatie.php";

Page::showHeader();
Page::showContent();

try
{
	$data			=   $_POST;
	
	$q = "UPDATE  `aparat` SET `ordinea`='".$data['ordinea']."',`data_autorizatie`='".$data['autorizatie']."',`data_inspectie`='".$data['inspectie']."', `nume` = '".$data['nume']."', `serie`='".$data['seria']."', `factor_mecanic`='".$data['factorM']."',`pret_impuls`='".$data['pretImpuls']."', `observatii`='".$data['observatii']."'  WHERE id = '".$data['id_aparat']."' ";
	$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()) or die(mysql_error());

	Page::showConfirmation('<span class="confirmation">Datele au fost modificate</span> <a href="optiuni_aparat.php?id_aparat='.$data['id_aparat'].'">Înapoi la optiuni aparat</a>');

}
catch(Exception $e)
{
	Page::showError($e->getMessage());
}

Page::showFooter();