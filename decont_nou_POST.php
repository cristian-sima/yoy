<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Design::showHeader();


try
{
	$data			=   $_POST;



	Procesare::checkRequestedData(array('data','suma', 'document','explicatie'),$data,'administreaza_deconturi.php');


	$mysql = "INSERT INTO `decont`
					(`explicatie`,
					`data`,
					`suma`,
					`document`)

					VALUES
					('".$data['explicatie']."',
					'".$data['data']."',
					'".$data['suma']."',
					'".$data['document']."')";

	$result = mysql_query($mysql, Aplicatie::getInstance()->Database);


	Design::showConfirmation('<span class="confirmation">Decontul a fost adăugat cu succes !</span> <a href="administreaza_deconturi.php ">Înapoi la deconturi</a>');

}
catch(Exception $e)
{
	Design::showError($e->getMessage());
}

Design::showFooter();
?>
