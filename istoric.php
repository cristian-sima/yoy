<?php

	require_once "include/php/Guvern.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/Procesare.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/SelectSituatie_GUI.php";

	Page::showHeader();
	Page::showContent();


	Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));

	$_criterii_MYSQL	= (($_GET['id_firma'] != '')?("AND i.idFirma='".$_GET['id_firma']."'"):(""));
	$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
	$totalS 			= 0;

	$selector_GUI->afiseazaButon(true);
	$selector_GUI->setAdresaButon("istoric.php");
	$selector_GUI->setTypeOfDocument("Istoric impozite premii");

	Page::showHeading("Vizualizați premii acordate", '
			<input class="disp" type="button" value="Tipărește" class="disp" onclick="window.print();" />
			'.(($_GET['id_firma'] != '')?('<input	class="disp" type="button" value="Acordă premiu" class="disp" onclick="document.location='."'".'acorda_premii.php?id_firma='.$_GET['id_firma']."'".';" />'):"").'');


	/* ---------------- content ---------------------*/


	$selector_GUI->display();

	$query = "SELECT  	i.id,
						i.data,
						i.idFirma,
						f.nume AS numeFirma,
						sum(i.suma) AS suma
				FROM impozit AS i
				LEFT JOIN firma AS f
				ON f.id=i.idFirma
				WHERE  data >= '".$selector_GUI->getDataCurenta()->getFirstDayOfMonth()."' AND data <= '".$selector_GUI->getDataCurenta()->getLastDayOfMonth()."' ".$_criterii_MYSQL."
				GROUP BY i.data
			";

	$result = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());

	if(mysql_num_rows($result) == 0)
	{
		echo 'Nu sunt premii acordate';
	}
	else
	{
		echo'
		<table id="example" class="display" width="100%">
			<tr class="head pad" >
				<td class="smoke"  >Data</td>
				<td class="smoke"  >Suma totală</td>
				<td  class="smoke disp">Vezi toate premiile</td>
			</tr>';

		while($premiu = mysql_fetch_array($result))
		{

			$totalS		+= $premiu['suma'];

			echo'
			<tr class="pad">
				<td>'.$premiu['data'].'</td>
				<td>'.$premiu['suma'].' lei</td>
				<td class="disp"><a href="istoric.php?data='.$premiu['data'].'&extinde=true&id_firma='.$_GET['id_firma'].'">Vezi detalii</a>
				</td>
			</tr>';
		}

		echo'
				<tr class="pad">
					<td  class="smoke">Total</td>
					<td  class="smoke">'.$totalS.' lei</td>
					<td   class="disp" class="smoke"></td>
				</tr>';
		echo'</table>';
	}

	echo'</br><br /><br />';

	if(isset($_GET['extinde']))
	{

		$prag_de_impozitare 	= Guvern::getPragDeImpozitare($selector_GUI->getDataCurenta());
		$procent_impozitare 	= Guvern::getProcentDeImpozitare($selector_GUI->getDataCurenta());

		$query = "SELECT	count(case WHEN i.suma>".$prag_de_impozitare." THEN 1 END) AS cate,
						i.id,
						sum(case WHEN i.suma>".$prag_de_impozitare." THEN i.suma ELSE 0 END) AS totalFirmaImpozabil,
						sum(i.suma) AS totalFirma,
						i.data,
						i.idFirma,
						f.id AS id_firma,
						f.nume AS denumire_firma
				FROM impozit AS i
				LEFT JOIN firma AS f
				ON f.id=i.idFirma
				WHERE  data = '".$selector_GUI->getDataCurenta()."'
				GROUP BY i.data, i.idFirma ";
	//echo $query;
		$result_zi = mysql_query($query, Aplicatie::getInstance()->getMYSQL()->getResource());

		if(mysql_num_rows($result_zi) == 0)
		{
			echo'Nu există date pentru această zi';
		}
		else
		{
			$totalF = 0;
			$totalI = 0;

			echo '
			<table id="example" class="display" width="100%">
				<tr class="head pad">
					<td class="smoke"  >Data</td><td  class="smoke">Firmă </td>
					<td  class="smoke"> Suma</td>
					<td >Sumă impozabilă</td>
					<td  class="disp" >Opțiuni</td>
				</tr>';

			while($premiu = mysql_fetch_array($result_zi))
			{
				$totalF		+= $premiu['totalFirma'];
				$totalI		+= $premiu['totalFirmaImpozabil'];

				echo'
				<tr class="pad">
					<td  class="smoke">'.$premiu['data'].'</td>
					<td  class="smoke">'.$premiu['denumire_firma'].'</td>
					<td  class="smoke">'.$premiu['totalFirma'].' lei</td>
					<td  class="smoke">'.$premiu['totalFirmaImpozabil'].' lei</td>
					<td class="disp"><a href="acorda_premii.php?id_firma='.$premiu['id_firma'].'&data='.$premiu['data'].'">Vezi persoanele</a>
				</tr>';
			}

			echo'
				<tr class="pad">
					<td  class="smoke">Total</td>
					<td  class="smoke"></td>
					<td  class="smoke">'.$totalF.' lei</td>
					<td  class="smoke">'.$totalI.' lei</td>
					<td class="disp"></td>
				</tr>';

			echo'</table>';
		}
	}



	/* ---------------- END of content ---------------------*/



	Page::showFooter();
	?>

	<style>
	#example tr:hover {
		background: rgb(255, 230, 184);
	}

	@page {
		size: portrait
	}

	.pad td {
		padding: 5px;
	}

	.head td {
		background: rgb(253, 241, 240);
		font-weight: bold;
	}

	#example tr td
	{
		border:1px solid #dfdfdf
	}
	</style>
