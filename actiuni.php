<?php
	
	require_once "include/php/Aplicatie.php";
	require_once "include/php/Procesare.php";
	require_once "include/php/SelectSituatie_GUI.php";
	
	Page::showHeader();
	Page::showContent();
	
	
	Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));	
	$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
	
	
	Page::showHeading("Intreprinde actiuni firma", "");
	
	
	$selector_GUI->afiseazaButon(false);
	$selector_GUI->afiseazaDescriere(false);
	$selector_GUI->afiseazaToateFirmele(false);
	$selector_GUI->afiseazaDoarFirmeActive(true);
	
	$selector_GUI->display();
	
	echo '
			<link href="include/css/fieldset.css" rel="stylesheet" type="text/css"/>
			<table width="50%">
				 <tr>
					 <td>
						<fieldset>
							<legend>Actiuni</legend>
							<a onclick="goTo('."'".'inchide_situatie_luna.php'."'".')" href="#" class="button blue medium bold">Închidere situatie lună</a>	<br /><br />
							<a onclick="goTo('."'".'administreaza_depuneri_de_numerar.php'."'".')" href="#" class="button orange medium bold">Adm. depunerile de  numerar</a>	<br /><br />	
							<a onclick="goTo('."'".'administreaza_deconturi.php'."'".')" href="#" class="button gray medium bold">Adm. deconturile </a>	<br /><br />	
							<a onclick="goTo('."'".'actualizeaza_situatie.php'."'".')" href="#" class="button gray medium bold">Actualizeaza situatii <span style="color:red">(numai in cazul in care este una din trecut modificata)</span> </a>	<br /><br />	
						</fieldset>
					</td>
				</tr>
		</table>	
	';
	
	Page::showFooter();
?>
					  