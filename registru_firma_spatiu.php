<?php 

	require_once "include/php/Aplicatie.php";
	require_once "include/php/Procesare.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/RegistruGUI.php";
	require_once "include/php/DataCalendaristica.php";
	require_once "include/php/SelectSituatie_GUI.php";
	require_once "include/php/RegistruGraficFirma.php";
	
	Login::permiteOperator();	
	Page::showHeader();
	Page::showContent();	
	Page::showHeading("Registru de Casa Firma", '<input type="button" class="disp" value="Printeaza" onclick="window.print()">&nbsp; 
	'.((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?('<a onclick="goTo('."'".'selecteaza_situatie.php'."'".')" href="#"><input type="button" value="Înapoi la Situații" /></a>
	'):('<a onclick="document.location = '."'".'situatie_mecanica_operator.php'."'".'" href="#"><input type="button" value="Înapoi la Situații" /></a>
	')).'
	');
	
	Procesare::createEmptyFields($_GET, array("data", "id_firma"));
	
	// forteaza utilizatorul sa aiba access doar la luna curenta și la firma lui
	$_GET['id_firma'] 	=	((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?$_GET['id_firma']:(Aplicatie::getInstance()->getUtilizator()->getIDFirma()));
	$_GET['data'] 		=	((Aplicatie::getInstance()->getUtilizator()->isAdministrator())?$_GET['data']:(new DataCalendaristica(date("Y-m-d"))));
	
	$firma					= new FirmaSpatiu($_GET['id_firma']);
	$data					= new DataCalendaristica($_GET['data']);	
	$selector				= new SelectSituatie_GUI($data, $_GET['id_firma']);	
	$registru_content		= new RegistruGraficFirma($firma, $data);	
	$registru_gui			= new RegistruGUI($registru_content);
	
	$selector->afiseazaToateFirmele(false);
	$selector->setAdresaButon("registru_firma_spatiu.php");
	$selector->afiseazaDescriere(false);
	$selector->adaugaCampSelect(array(	"denumire" 	=> "",
									  	"id"		=> "afiseaza_totaluri",
										"optiuni"	=> array(
															array(
																"denumire" => "Afiseaza totaluri",
																"valoare" => ""
															),
															array(
																"denumire" => "Nu afisa totaluri",
																"valoare" => "false"
															)
											)));
	$registru_gui->afiseazaTotalurile(!$selector->getValoareOptiune('afiseaza_totaluri'));	
	
	if(Aplicatie::getInstance()->getUtilizator()->isAdministrator())
	{
		$selector->display();	
	}
	
	$registru_gui->afiseaza();


	Page::showFooter();	
?>	