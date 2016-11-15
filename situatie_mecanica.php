	<?php


	require_once "app/Procesare.php";
	require_once "app/FirmaSpatiu.php";
	require_once "app/Aplicatie.php";
	require_once "app/Guvern.php";
	require_once "app/Utilizator.php";
	require_once "app/Situatie_GUI.php";
	require_once "app/SituatieMecanicaGraficaCompletaAzi.php";

	$GUI = "";
	$html = "";

	// for sql_injection
	foreach ($_GET as $index => $value)
	{
		$_GET[$index] 	= mysql_real_escape_string($value);
	}


	Procesare::createEmptyFields($_GET, array("id_firma","from","to"));

	$numar_de_randuri		= 0;


	if($_GET['from'] == '')
	{
		$_GET['from'] = $_GET['to'] = date("Y-m-d");
	}


	try
	{
		$firma		= new FirmaSpatiu($_GET['id_firma']);
		$today		= new DataCalendaristica(date("Y-m-d"));
		$data1 		= new DataCalendaristica($_GET['from']);

		if((isset($_GET['month'])))
		{
			$_GET['to'] = $data1->getLastDayOfMonth();
		}

		if(strtotime($_GET['to']) > strtotime($today))
		{
			$_GET['to']	= $today;
		}

		$data2 		= new DataCalendaristica($_GET['to']);

		$ultima_data			= SituatieMecanica::getUltimaCompletareStrict($firma, $data1);
		$urmatoarea_data		= SituatieMecanica::getUrmatoareaCompletareStrict($firma, $data1);

	}
	catch(Exception $e)
	{
		Design::complain($e->getMessage());
	}

	/*---------------------- Afisarea un interval ---------------*/

	if(isset($_GET['type']) && ($_GET['type'] == 'afisarea'))
	{
		while(strtotime($data1) <= strtotime($data2))
		{
			$situatie	= new SituatieMecanicaGraficaCompleta($data1, $firma);


			if(!$situatie->isFake())
			{
				$GUI		= new Situatie_GUI($situatie, $firma);
			}
			else
			{
				$GUI		= new Situatie_GUI($situatie, $firma);
				$GUI->displayAutor(false);
			}

			$GUI->isInteractiva(false);

			$data1 = new DataCalendaristica(DataCalendaristica::getZiuaUrmatoare($data1));
		}

	}
	else
	{
		/*----------------------- Totalizare perioada ------------------*/

		if($data1 != $data2)
		{


			$situatie			= new SituatieMecanicaGrafica($data1, $data2, $firma);
			$GUI				= new Situatie_GUI($situatie, $firma);
			$numar_de_randuri	= $situatie->getNumarulDeAparate();

			$GUI->isInteractiva(false);
			$GUI->displayAutor(false);
		}
		else
		{
			/*---------------------- Totalizare o data diferita de azi ---------------*/

			if($data1.'' != $today.'')
			{
				$situatie				= new SituatieMecanicaGraficaCompleta($data1, $firma);
				$numar_de_randuri		= $situatie->getNumarulDeAparate();
				$GUI					= new Situatie_GUI($situatie, $firma);

				if($situatie->isFake())
				{
					$GUI->isInteractiva(false);
				}

			}
			else
			{

			/*---------------------- Totalizare astazi ---------------*/

				$situatie			= new SituatieMecanicaGraficaCompletaAzi($firma);
				$numar_de_randuri	= $situatie->getNumarulDeAparate();
				$GUI				= new Situatie_GUI($situatie, $firma);


				$numar_de_randuri	= $situatie->getNumarulDeAparate();

			}
		}
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
		<div id="control_panel" class="disp">
				<table width="100%" style="border-bottom:1px solid #dfdfdf;">
				<tr>
				<td width="80%">
			De la <input type="text" id="from" class="datepicker" value="'.$data1.'" placeholder="De la"/>
			până la <input type="text" id="to" class="datepicker" value="'.$data2.'" placeholder="Până la"/>
			 doresc

			<select id="_type">
			<option value="total" '.(((isset($_GET['type']))&&($_GET['type']=="total"))?"selected='selected'":"").'>Totalizarea</option>
			<option value="afisarea" '.(((isset($_GET['type']))&&($_GET['type']=="afisarea"))?"selected='selected'":"").'>Afișarea</option>
			</select>&nbsp;&nbsp;<input type="button" value="Procesează !" onclick="seeData();" id="viz_" />
			';
		echo '</td><td style="text-align:right">';
		echo '<a style="display:none"></a><a href="company_details.php?id='.$firma->getID().'"><input type="button" value="Interfață firmă" /></a>&nbsp;
				<a href="aparate.php?id='.$firma->getID().'"><input type="button" title="Lista cu aparate din firmă" value="Aparate" /></a>';
		echo '</td></tr><tr><td width="50%">';
		echo 'Anul <select id="an">';
		for($anul=2013; $anul<=2020; $anul++)
		{
			echo'<option value="'.$anul.'" '.((intval($data1->getAnul()) == $anul)?"selected":"").'>'.$anul."</option>";
		}
		echo "</select>";
		echo '&nbsp;&nbsp;&nbsp;&nbsp;';
		echo 'Luna <select id="luna">';
		for($luna=1; $luna<=12; $luna++)
		{
			echo'<option value="'.$luna.'"'.((intval($data1->getLuna()) == $luna)?"selected":"").' >'.DataCalendaristica::getNumeleLunii($luna)."</option>";
		}
		echo "</select>";
		echo '&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Vezi situație lunară" onclick="seeData2();" />';
		echo'</td><td width="50%" style="text-align:right">';
		echo'</td></tr></table>';
		echo '<table width="100%" id="dispare3"><tr><td style="">';
		echo '<input title="'.$ultima_data.'"'.(((isset($_GET['month']))) || ($ultima_data == null)?'disabled="disabled"':'').' type="button" id="a" value="Completarea precedentă" onclick="document.location='."'".'situatie_mecanica.php?id_firma='.$firma->getID().'&from='.$ultima_data.'&to='.$ultima_data."'".'"/>';
		echo '<input title="'.$today.'"'.(($data1 == $today)?'disabled="disabled"':'').' type="button" id="a" value="Ziua curentă" onclick="document.location='."'".'situatie_mecanica.php?id_firma='.$firma->getID().'&from='.$today.'&to='.$today."'".'"/>';
		echo '<input title="'.$urmatoarea_data.'"'.(((isset($_GET['month']))) || ($urmatoarea_data == null)?'disabled="disabled"':'').' type="button" id="a" value="Următoarea completare" onclick="document.location='."'".'situatie_mecanica.php?id_firma='.$firma->getID().'&from='.$urmatoarea_data.'&to='.$urmatoarea_data."'".'"/>';
		echo '</td><td style="text-align:right">&nbsp;&nbsp;&nbsp;';
		echo '<a href="situatie_mecanica.php?id_firma='.$firma->getID().'&from='.$data1.'&to='.$data2.'&type=PDF"><input type="button" value="Descarcă situație" /></a>&nbsp;&nbsp;&nbsp;';
		echo '<input type="button" value="Tipărește situatie"  onclick="window.print()" />&nbsp;&nbsp;&nbsp;';
		echo '<input type="button" class="mod bold" id="salveaza_modificari" value="Salvează modificările !" onclick="beforeSubmit()" />';
		echo '</td></tr></table></div><br />';

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
		$title = "YOY.ro Situația mecanică ".(($data1.'' !== $data2.'')?("de la ".$data1." la ".$data2):("la data ".$data1))." pentru firma ".$firma->getDenumire();


		header("Content-Type", "text/html; charset=utf-8");
		require_once 'vendor/pdf/dompdf_config.inc.php';
		mb_internal_encoding('UTF-8');
		$ready =  '<html><head><title>'.$title.'</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><link href="public/css/pdf.css" rel="stylesheet" type="text/css"/></head><body>'.$html."</body></html>";
		$dompdf = new DOMPDF();
		$dompdf->set_paper('a4', 'landscape');
		$dompdf->load_html($ready, 'UTF-8');
		$dompdf->render();
		$dompdf->stream($title.".pdf");
	}
