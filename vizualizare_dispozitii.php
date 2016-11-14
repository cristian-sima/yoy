
	<?php

	require_once "app/Guvern.php";
	require_once "app/Aplicatie.php";
	require_once "app/Procesare.php";
	require_once "app/FirmaSpatiu.php";
	require_once "app/SelectSituatie_GUI.php";

	Design::showHeader();
	


	Procesare::createEmptyFields($_GET, array ('id_firma', 'data'));

	$_criterii_MYSQL	= (($_GET['id_firma'] != '')?("AND _to='".$_GET['id_firma']."'"):(""));
	$selector_GUI		= new SelectSituatie_GUI($_GET['data'], $_GET['id_firma']);
	$total_plati		= 0;
	$total_incasari		= 0;

	$selector_GUI->afiseazaButon(true);
	$selector_GUI->setAdresaButon("vizualizare_dispozitii.php");
	$selector_GUI->setTypeOfDocument("Dispoziții");

	Design::showHeading("Vizualizați dispoziții", '
			<input class="disp" type="button" value="Tipărește" class="disp" onclick="window.print();" />
			<input	class="disp" type="button" value="Adaugă dispoziție" class="disp" onclick="document.location='."'".'dispozitie_operator_noua.php'."'".';" />
		');


	/* ---------------- content ---------------------*/


	$selector_GUI->display();

	$query = "SELECT
					sum(case WHEN tip = 'plata' THEN valoare ELSE 0 END) AS valoareTotalPlati,
					sum(case WHEN tip = 'incasare' THEN valoare ELSE 0 END) AS valoareTotalIncasari,
					data
					FROM dispozitie
					WHERE  data >= '".$selector_GUI->getDataCurenta()->getFirstDayOfMonth()."' AND data <= '".$selector_GUI->getDataCurenta()->getLastDayOfMonth()."' ".$_criterii_MYSQL."
					GROUP BY data
					ORDER by data DESC
			";

	$result = mysql_query($query, Aplicatie::getInstance()->Database);

	if(mysql_num_rows($result) == 0)
	{
		echo 'Nu sunt dispoziții';
	}
	else
	{
		echo'
		<table id="example" class="display" width="100%">
			<tr class="head pad" >
				<td class="smoke"  >Data</td>
				<td  class="smoke"> Total plați</td>
				<td  class="smoke"> Total încasări</td>
				<td  class="smoke disp"> Opțiuni</td>
			</tr>';

		while($dispozitie = mysql_fetch_array($result))
		{
			$total_plati	+= 	$dispozitie['valoareTotalPlati'];
			$total_incasari	+=	$dispozitie['valoareTotalIncasari'];
			echo'
			<tr class="pad">
				<td>'.$dispozitie['data'].'</td>
				<td>'.$dispozitie['valoareTotalPlati'].' lei</td>
				<td>'.$dispozitie['valoareTotalIncasari'].' lei</td>
				<td class="disp"><a href="vizualizare_dispozitii.php?data='.$dispozitie['data'].'&extinde=true">Vezi detalii</a>
				</td>
			</tr>';
		}

		echo '	<tr class="pad">
				<td class="bold">Total</td>
				<td>'.$total_plati.' lei</td>
				<td>'.$total_incasari.' lei</td>
				<td class="disp">
				</td>
			</tr>';

		echo'</table>';
	}

	echo'</br><br /><br />';

	if(isset($_GET['extinde']))
	{

		$query = "SELECT
					d.id,
					d.data,
					d._to,
					d.tip,
					d.valoare,
					d.document,
					d.explicatie,
					(SELECT nume FROM `firma` AS f WHERE f.id = d._to) AS denumire_firma
				FROM dispozitie AS d
				WHERE  data='".$selector_GUI->getDataCurenta()."' ".$_criterii_MYSQL."";

		$result_zi = mysql_query($query, Aplicatie::getInstance()->Database);

		if(mysql_num_rows($result_zi) == 0)
		{
			echo'Nu există date pentru această zi';
		}
		else
		{
			echo '
			Perspectiva de plată/încasare este pentru '.Aplicatie::getInstance()->getFirmaOrganizatoare()->getDenumire().'<br />
			<table id="example" class="display" width="100%">
				<tr class="head pad">
					<td class="smoke"  >Data</td><td  class="smoke">Firmă </td>
					<td  class="smoke">Tip </td>
					<td >Valoare</td>
					<td >Document</td>
					<td >Explicație</td>
				</tr>';

			while($dispozitie = mysql_fetch_array($result_zi))
			{
				echo'
				<tr class="pad">
					<td  class="smoke">'.$dispozitie['data'].'</td>
					<td  class="smoke">'.$dispozitie['denumire_firma'].'</td>
					<td> <img src="public/images/'.$dispozitie['tip'].'.png" > '.($dispozitie['tip'] == "incasare" ? "încasare" : "plată").'</td>
					<td style="'.(($dispozitie['tip']=='plata')?"color:red":"color:green").'">'.$dispozitie['valoare'].' lei</td>
					<td class="smoke" style="">'.htmlspecialchars($dispozitie['document']).'</td>
					<td class="smoke" style="">'.htmlspecialchars($dispozitie['explicatie']).'</td>
				</tr>';
			}

			echo'</table>';
		}
	}



	/* ---------------- END of content ---------------------*/



	Design::showFooter();
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
