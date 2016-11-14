
	<?php


	require_once "app/Procesare.php";
	require_once "app/FirmaSpatiu.php";
	require_once "app/Aplicatie.php";
	require_once "app/Guvern.php";
	require_once "app/Utilizator.php";
	require_once "app/Situatie_GUI.php";
	require_once "app/SituatieMecanicaGraficaCompletaAzi.php";


	$html = "";
	$GUI = "";

	Login::permiteOperator();


	// for sql_injection
	foreach ($_GET as $index => $value)
	{
		$_GET[$index] 	= mysql_real_escape_string($value);
	}


	$numar_de_randuri		= 0;

	$_GET['id_firma'] = Aplicatie::getInstance()->getUtilizator()->getIdFirma();

	try
	{
		$firma					= new FirmaSpatiu($_GET['id_firma']);
		$today					= new DataCalendaristica(date("Y-m-d"));
		$ultima_data			= SituatieMecanica::getUltimaCompletareStrict($firma, $today);

		if(isset($_GET['last']))
		{

			if($ultima_data != null)
				$_GET['to'] = $_GET['from'] = $ultima_data;
		}
		else
			$_GET['from'] = $_GET['to'] = $today;

		$data1 		= new DataCalendaristica($_GET['from']);
		$data2 		= new DataCalendaristica($_GET['to']);
	}
	catch(Exception $e)
	{
		Design::complain($e->getMessage());
	}






	/*---------------------- Totalizare o data diferita de azi ---------------*/

	if($data1.'' != $today.'')
	{
		$situatie	= new SituatieMecanicaGraficaCompleta($data1, $firma);
		$numar_de_randuri	= $situatie->getNumarulDeAparate();
		$GUI		= new Situatie_GUI($situatie, $firma);
		$GUI->isInteractiva(false);
	}
	else
	{

	/*---------------------- Totalizare astazi ---------------*/

		$situatie	= new SituatieMecanicaGraficaCompletaAzi($firma);
		$numar_de_randuri	= $situatie->getNumarulDeAparate();

		$GUI		= new Situatie_GUI($situatie, $firma);

	}


	if(!isset($_GET['type']) || (isset($_GET['type']) && $_GET['type'] != "PDF")){

	Design::showHeader();
	


	Design::showHeading('<img src="public/images/results.png" width="64px" height="64px" />  Situație firma '.$firma->getDenumire().'</b>', "");


	echo '
	<script src="public/js/situatie.js"></script>
	<script>
				situatie.firma = '.$firma->getID().';
	</script>';

	echo '
	<div id="control_panel" class="hidden-print">
		<table width="100%" style="border-bottom:1px solid #dfdfdf;">
			<tr>

				<td width="60%"><input type="button" '.((($ultima_data == null) || ($data1.'' != $today.''))?("disabled"):("")).' value="Vezi ultima situație completată" onclick="document.location='."'".'situatie_mecanica_operator.php?last=true'."'".'" />&nbsp;&nbsp;
					<input type="button" id="a" value="Data curentă" '.(($today.'' == $data1.'')?("disabled"):("")).' onclick="document.location='."'".'situatie_mecanica_operator.php'."'".'"/>
				</td>
				<td style="text-align:right">
					<input type="button" class="mod bold" '.((($ultima_data == null) || ($data1.'' != $today.''))?("disabled"):("")).' title="Salvează modificările" id="salveaza_modificari" value="Salvează modificările !" onclick="beforeSubmit()" />
				</td>
			</tr>
			<tr>
				<td width="50%">
				</td>
				<td width="50%" style="text-align:right">
				</td>
			</tr>
		</table>
		<table width="100%" >
			<tr>
				<td style="">
					<input type="button"   value="Vezi registru de casă" onclick="document.location='."'".'registru_firma_spatiu.php?from='.$data1->getAnul().'-'.$data1->getLuna()."'".'" />
					<input type="button"   value="Adaugă dispoziție" onclick="document.location='."'".'dispozitie_operator_noua.php?from='.$data1->getAnul().'-'.$data1->getLuna()."'".'" />
				</td>
				<td style="text-align:center"> &nbsp;&nbsp;&nbsp;
					<input type="button" value="Tipărește situație"  onclick="window.print()" />
				</td>
				<td>
				'; echo '&nbsp;&nbsp;&nbsp;<a href="situatie_mecanica_operator.php?id_firma='.$firma->getID().'&from='.$data1.'&to='.$data2.'&type=PDF'.(isset($_GET['last'])?"&last=true":"").'"><input type="button" value="Descarcă situație" /></a>';

				echo '


				</td>
			</tr>
		</table>
	</div>';

	$GUI->display();

	echo '	<script>
			situatie.nrDeAparate = '.$numar_de_randuri.';
			</script>';

		Design::showFooter();

	}
	else {
		// generate PDF
		$GUI->isInteractiva(false);
		$GUI->isPaper();
		$html =  $GUI->getHTML();
		$title = "YOY.ro Situația mecanică din ".$data1." pentru firma  ".$firma->getDenumire();

		header("Content-Type", "text/html; charset=utf-8");
		require_once 'vendor/pdf/dompdf_config.inc.php';
		mb_internal_encoding('UTF-8');
		$ready =  '<html><head><title>'.$title.'</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><link href="public/css/pdf.css" rel="stylesheet" type="text/css"/></head><body>'.$html."</body></html>";

		$dompdf = new DOMPDF();
		$dompdf->set_paper('a4', 'landscape');
		$dompdf->load_html($ready, 'UTF-8');
		$dompdf->render();
		$dompdf->stream($title . ".pdf");
	}
