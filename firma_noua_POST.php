<?php

	require_once "app/Procesare.php";
	require_once "app/Aplicatie.php";

	Page::showHeader();
	Page::showContent();

	try
	{
		$data			=   $_POST;

		Procesare::checkRequestedData(
							array('nume','localitate','procent'),
							$data,
							'firma_noua.php');
		Procesare::createEmptyFields($data, array("comentarii","date_contact"));


		// content

		$mysql = "INSERT INTO `firma`(`nume`, `localitate`, `dateContact`, `comentarii`) VALUES ('".$data['nume']."','".$data['localitate']."','".$data['comentarii']."','".$data['date_contact']."')";
		$result = mysql_query($mysql, Aplicatie::getInstance()->Database);


		$mysql = "SELECT id FROM firma ORDER BY id DESC LIMIT 0,1";
		$result = mysql_query($mysql, Aplicatie::getInstance()->Database);
		while($row = mysql_fetch_array($result))
		{
			$id=	$row['id'];
		}


		$mysql = "INSERT INTO `procent`(`idFirma`,`valoare`, `_from`, `_to`, `isNow`) VALUES ('".$id."','".$data['procent']."','".DataCalendaristica::getZiuaPrecedenta(date("Y-m-1"))."','".date("Y-m-d")."','1')";
		$result = mysql_query($mysql, Aplicatie::getInstance()->Database);


		// $mysql = "INSERT INTO completare(`idFirma`,`_when`) VALUES('".$id."','".date('Y-m-d')."')";
		// $result3 = mysql_query($mysql, Aplicatie::getInstance()->Database) or die(mysql_error());

		Page::showConfirmation('<span class="confirmation">Firma a fost adăugată</span> <a href="space_companies.php ">Înapoi</a>');	

	}
	catch(Exception $e)
	{
		Page::showError($e->getMessage());
	}

	Page::showFooter();
?>
