<?php

	require_once "app/Aplicatie.php";
	require_once "app/Procesare.php";
	require_once "app/SelectSituatie_GUI.php";

	Page::showHeader();
	Page::showContent();


	try {
		Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));
		$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
		$selector_GUI->afiseazaToateFirmele(false);
		$selector_GUI->afiseazaDescriere(false);
		$selector_GUI->afiseazaButon(false);

		Page::showHeading("Selectează o situație", "");

		$selector_GUI->display();

		echo '
			<link href="public/css/fieldset.css" rel="stylesheet" type="text/css"/>
			<table style="width:100%">
				<tr>
					<td width="50%" style="vertical-align: top">
						<fieldset>
							<legend>Registre</legend>
							<a onclick="goTo('."'".'registru_firma_spatiu.php'."'".')" href="#"
								class="button gray medium bold">Registru firmă</a><br /> <br /> <a
								onclick="goTo('."'".'registru_general_lunar.php'."'".')" href="#"
								class="button blue medium bold">Registru general</a><br /> <br /> <a
								onclick="goTo('."'".'registru_central_lunar.php'."'".')" href="#"
								class="button green medium bold">Registru central</a><br /> <br />
						</fieldset>
						</td>
						<td width="50%" style="vertical-align: top">
						<fieldset>
							<legend>Alte situații</legend>
							<a onclick="goTo('."'".'situatie_lunara.php'."'".')" href="#"
								class="button orange medium bold">Situație lunară</a> <br /> <br />
							<a onclick="goTo('."'".'afisare_decont_firma.php'."'".')" href="#"
								class="button medium green bold">Decont firmă</a> <br /> <br />
						</fieldset>
						</td>
					</tr>
			</table>
		';

		Page::showFooter();
	} catch (Exception $e) {
		PAGE::showError($e->getMessage());
	}

?>
