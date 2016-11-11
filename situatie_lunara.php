<?php

	require_once "include/php/Procesare.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/RegistruGUI.php";
	require_once "include/php/RegistruGrafic.php";
	require_once "include/php/DataCalendaristica.php";
	require_once "include/php/SelectSituatie_GUI.php";

	Page::showHeader();
	Page::showContent();
	Page::showHeading("Situație lunară", '<input type="button" class="disp" value="Tipărește" onclick="window.print()">&nbsp; <a onclick="goTo('."'".'selecteaza_situatie.php'."'".')" href="#"><input type="button" value="Înapoi la situații" /></a>');

	Procesare::createEmptyFields($_GET, array("data", "id_firma", "afiseaza_totaluri"));

	$data					= new DataCalendaristica($_GET['data']);
	$selector				= new SelectSituatie_GUI($data, $_GET['id_firma']);
	$registru_gui 		 	= new RegistruGUI($registru_content);

	$selector->afiseazaFirme(false);
	$selector->setAdresaButon("situatie_lunara.php");
	$selector->afiseazaDescriere(false);
	$selector->adaugaCampSelect(array(	"denumire" 	=> "",
									  	"id"		=> "afiseaza_totaluri",
										"optiuni"	=> array(
															array(
																"denumire" => "Afișează totaluri",
																"valoare" => ""
															),
															array(
																"denumire" => "Nu afișa totaluri",
																"valoare" => "false"
															)
														)));


	$selector->display();
	$registru_gui->afiseazaSemnaturi(false);
	$registru_gui->afiseazaSoldInceput(false);
	$registru_gui->afiseazaTotalurile(!$selector->getValoareOptiune('afiseaza_totaluri'));
	$registru_gui->afiseaza();

	Page::showFooter();
?>
