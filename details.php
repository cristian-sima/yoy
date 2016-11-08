<?php

	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	require_once "include/php/SituatieMecanica.php";

	Page::showHeader();
	Page::showContent();


	$firma 				= new FirmaSpatiu($_GET['idFirma']);
	$data				= new DataCalendaristica(date("Y-m-d"));
	$ultima_data		= SituatieMecanica::getUltimaCompletare($firma, $data);


	if($ultima_data == null)
	{
		$ultima_data 	= "Niciodata";
		$zileTrecute	= "";
	}
	else
	{
		$now 			= time(); // or your date as well
		$datediff 		= $now - strtotime($ultima_data);
		$dif 			= (floor($datediff/(60*60*24)));
		$zileTrecute 	= '( '.(($dif==1)?"o zi in urma":(($dif==0)?"Astazi":($dif." zile".'  in urma'))).' )';
	}

	?>
		<link href="include/css/butoane_pacanele.css" rel="stylesheet" type="text/css" />
		<link href="include/css/fieldset.css" rel="stylesheet" type="text/css"/>
		<fieldset>
		<legend>
		<?php echo '<span class="bold" style="color:orange">'.$firma->getDenumire().'</span>';?>
		</legend>
		<table width="100%">
			<tr>
				<td width="50%">
					<table width="300px">
						<tr>
							<td width="50%" class="smoke">Localitate</td>
							<td width="50%"><?php echo '<span class="bold" style="color:gray">'.$firma->getLocatie().'</span>';?>
							</td>
						</tr>
						<tr>
							<td width="50%" class="smoke">Statut firmă</td>
							<td width="50%"><?php $color = (($firma->isActiva())?"green":"red"); echo '<span style="color:'.$color.'">'.(($firma->isActiva())?"contract activ":"contract incetat")."
										</span>";?>
							</td>
						</tr>
						<tr>
							<td width="50%" class="smoke">Procent</td>
							<td width="50%"><?php echo $firma->getProcentFirma($data);?>%</td>
						</tr>
						<!--
									<tr>

										<td width="50%" class="smoke">
											Restanta
										</td>

										<td width="50%">
											<?php //echo $firma['restanta'];?> <span class="smoke">lei</span>
										</td>
									</tr>
									-->
					</table>
				</td>
				<td width="50%"><a
					onclick="document.location='situatie_mecanica.php?id_firma=<?php echo$firma->getID(); ?>'"
					class="button green medium">Situatie zilnica</a><br /> <span
					class="smoke" style="font-size: 12px">Ultima completare: <?php echo$ultima_data.' '.$zileTrecute; ?>
				</span>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend class="bold">Situatii</legend>
		<table width="100%">
			<tr>
				<td width="50%" class="smoke"><?php

				echo'Anul <select id="an">';

				for( $i=2013;$i<=2020;$i++)
				{
					echo'<option value="'.$i.'" '.(($i==$data->getAnul())?("selected"):"").'>'.$i."</option>";
				}
				echo"</select>";

				echo'&nbsp;&nbsp;&nbsp;&nbsp;Luna <select id="luna">';

				for( $luna=1; $luna<=12; $luna++)
				{
					echo'<option value="'.$luna.'" '.(($luna == $data->getLuna())?("selected"):"").' >'.DataCalendaristica::getNumeleLunii($luna)."</option>";
				}
				echo"</select>";

				?>
				</td>
				<td width="50%" style="text-align: right"><?php if($firma->isActiva()){ ?>
					<a href="acorda_premii.php?id_firma=<?php echo $firma->getID(); ?>"
					class="button orange small bold">Acorda premii</a> <a
					onclick="seeData('inchide_situatie_luna')" href="#"
					class="button orange small bold">Inchidere luna</a> <a
					onclick="seeData('editare_date_firma')" href="#"
					class="button orange small bold">Edit. date firma</a> <?php } ?>
				</td>
			</tr>
		</table>

		<br /> <br /> <a onclick="seeData('istoric')" href="#"
			class="button blue small bold">Istoric impozit</a> <a
			onclick="seeData('incasari')" href="#" class="button gray small bold">Incasari</a>
		<a onclick="seeData('vizualizare_dispozitii')" href="#"
			class="button blue small bold">Dispozitii</a> <a
			onclick="seeData('registru_firma_spatiu')" href="#"
			class="button gray small bold">Reg. firma</a> <a
			onclick="seeData('afisare_decont_firma')" href="#" class="button blue small bold">Decont</a>

	</fieldset>

	<fieldset>

		<legend class="bold">Aparate</legend>

		<table width="100%">
			<tr>
				<td><?php if($firma->isActiva()!='0'){?><input type="button"
					value="Aparat nou"
					onclick="document.location='adauga_aparat.php?id_firma=<?php echo$firma->getID();?>'" />
					<?php }?>
				</td>
				<td style="text-align: right"></td>
			</tr>
		</table>

		<br />

		<div style=" margin-left: 13px;">
			<table cellpadding="0" cellspacing="0" border="0" class="display"
				id="example" style="">
				<thead>
					<tr>
						<th>Nr</th>
						<th>Seria</th>
						<th>Numele ap.</th>
						<th>Exp. autoriz.</th>
						<th>Exp. insp. tech.</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$q = "SELECT * FROM `aparat` WHERE activ='1' AND id_firma='".$firma->getID()."' order by ordinea ASC";

				$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
				while($row = mysql_fetch_array($result)){
					echo'<tr onclick="document.location='."'"."optiuni_aparat.php?situatie=true&id_aparat=".$row['id']."&id_firma=".$row['id_firma']."'".'" class="hover" >
						<td >'.$row['ordinea'].'</td>
						<td >'.$row['serie'].'</td>
						<td>'.$row['nume'].'</td>
						<td >'.$row['data_autorizatie'].'</td>
						<td >'.$row['data_inspectie'].'</td>';

					echo'</tr>';
				}?>
				</tbody>
			</table>
		</div>

	</fieldset>

	<fieldset>
		<legend class="bold">Operatori</legend>

		<table width="100%">
			<tr>
				<td><?php if($firma->isActiva()){ ?> <input type="button"
					value="Administrator nou"
					onclick="document.location='adauga_utilizator.php?type=admin'" /> <input
					type="button" value="Operator nou"
					onclick="document.location='adauga_utilizator.php?type=normal'" /> <?php } ?>
				</td>
				<td style="text-align: right"></td>
			</tr>
		</table>

		<br />

		<div style=" margin-left: 13px;">
			<table cellpadding="0" cellspacing="0" border="0" class="display"
				id="example2" style="">
				<thead>
					<tr>
						<th>Utilizator</th>
						<th>Tip cont</th>
						<th>Optiuni</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$q = "SELECT * FROM `utilizator` WHERE activ='1' AND idFirma = '".$_GET['idFirma']."'";
				$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
				while($row = mysql_fetch_array($result))
				{
					echo'
					<tr >
					<td >'.$row['nume'].'(<span class="smoke">'.$row['user'].'</span>)</td>

					<td>'.(($row['tipCont']=="admin")?"Administrator":"Operator (".$row['tipOperator'].')').'</td>
					<td><input type="button" value="Editeaza date" onclick="document.location='."'".'editare_date_utilizator.php?id_user='.$row['id'].''."'".'"/>';
					if($firma->isActiva()!='0'){ echo'<input type="button" value="Dezactiveaza contul" onclick="document.location='."'".'activeaza_utilizator.php?id_user='.$row['id'].'&type=0'."'".'" />'; }
					echo'</td>
					</tr>';
				}
				?>
				</tbody>
			</table>
		</div>

	</fieldset>

	<script>
		$(document).ready(function() {
		    $('#example').dataTable({	"bJQueryUI": true,
							"sPaginationType": "full_numbers"});
		} );

		$(document).ready(function() {
		    $('#example2').dataTable({	"bJQueryUI": true,
							"sPaginationType": "full_numbers"});
		} );

		function seeData(where)
		{
			document.location = where+".php?id_firma=<?php echo$firma->getID(); ?>&data="+$("#an").val()+"-"+$("#luna").val()+"-01";
		}
	</script>

<?php

		Page::showFooter();
