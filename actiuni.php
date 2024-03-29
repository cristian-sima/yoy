<?php

	require_once "app/Aplicatie.php";
	require_once "app/Procesare.php";
	require_once "app/SelectSituatie_GUI.php";

	Design::showHeader();
	


	Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));
	$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);


	Design::showHeading("Întreprinde acțiuni firmă", "");


	$selector_GUI->afiseazaButon(false);
	$selector_GUI->afiseazaDescriere(false);
	$selector_GUI->afiseazaToateFirmele(false);
	$selector_GUI->afiseazaDoarFirmeActive(true);

	$selector_GUI->display();

	echo '
			<link href="public/css/fieldset.css" rel="stylesheet" type="text/css"/>
			<table width="50%">
				 <tr>
					 <td>
						<fieldset>
							<legend>Acțiuni</legend>
							<a onclick="goTo('."'".'inchide_situatie_luna.php'."'".')" href="#" class="button blue medium bold">Închidere situație lună</a>	<br /><br />
							<a onclick="goTo('."'".'depuneri.php'."'".')" href="#" class="button orange medium bold">Adm. depunerile de numerar</a>	<br /><br />
							<a onclick="goTo('."'".'administreaza_deconturi.php'."'".')" href="#" class="button gray medium bold">Adm. deconturile </a>	<br /><br />
						</fieldset>
					</td>
				</tr>
		</table>
	';

	Design::showFooter();
?>
