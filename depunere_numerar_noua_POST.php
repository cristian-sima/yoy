<?php

require_once "app/Procesare.php";
require_once "app/Aplicatie.php";

Design::showHeader();


try
{
	$data			=   $_POST;



	Procesare::checkRequestedData(array('data','suma', 'document','explicatie'),$data,'depuneri.php');


	$mysql = "INSERT INTO `depunere_numerar`
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


	Design::showConfirmation('<span class="confirmation">Depunerea a fost adaugată cu succes !</span> <a href="depuneri.php ">Înapoi la depuneri</a>');

}
catch(Exception $e)
{
	Design::showError($e->getMessage());
}

Design::showFooter();
?>
