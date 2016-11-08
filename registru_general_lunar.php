<?php 

	require_once "include/php/Aplicatie.php";
	require_once "include/php/Procesare.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/RegistruGUI.php";
	require_once "include/php/DataCalendaristica.php";
	require_once "include/php/SelectSituatie_GUI.php";
	require_once "include/php/RegistruGraficGeneral.php";
	
	
	Page::showHeader();
	Page::showContent();	
	Page::showHeading("Registru General", '<input type="button" class="disp" value="Tipărește" onclick="window.print()">&nbsp; <a onclick="goTo('."'".'selecteaza_situatie.php'."'".')" href="#"><input type="button" value="Înapoi la situații" /></a>');
		
	Procesare::createEmptyFields($_GET, array("data", "id_firma"));
	
	$data					= new DataCalendaristica($_GET['data']);	
	$selector				= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);	
	$registru_content		= new RegistruGraficGeneral($data);	
	$registru_gui			= new RegistruGUI($registru_content);
	
	$selector->afiseazaFirme(false);
	$selector->setAdresaButon("registru_general_lunar.php");
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
	$registru_gui->afiseazaTotalurile(!$selector->getValoareOptiune('afiseaza_totaluri'));	
	
	
	$selector->display();	
	$registru_gui->afiseaza();

	Page::showFooter();	
?>	