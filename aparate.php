<?php

	require_once "include/php/Aparat.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";

	Page::showHeader();
	Page::showContent();

	$firma	= new FirmaSpatiu($_GET['id']);


echo '
<table id="heading">
	<tr>
		<td>Vizualizați aparatele curente din firma <b>'.$firma->getDenumire().'
		</b>
		</td>
		<td style="text-align: right">
		';
		if($firma->isActiva())
		{
			?><input type="button" value="Adaugă aparat"
			onclick="document.location='adauga_aparat.php?id_firma=<?php echo $firma->getID();?>'" />
			<?php
		}
		?> <input type="button" value="Înapoi la situație"
			onclick="document.location='situatie_mecanica.php?id_firma=<?php echo $firma->getID();?>'" />
		<?php
		echo '
		</td>
	</tr>
</table>

<div style="width: 958px; margin-top:10px; margin-left: 13px;">
	<table cellpadding="0" cellspacing="0" border="0" class="display"
		id="example" style="">
		<thead>
			<tr>
				<th>Nr</th>
				<th>Seria</th>
				<th>Numele ap.</th>
				<th>Exp. autoriz.</th>
				<th>Exp. insp. tech.</th>
				<th>Locatie curenta</th>
			</tr>
		</thead>
		<tbody>';

		$q = "SELECT aparat.*,(SELECT `nume` FROM `firma` WHERE firma.id=aparat.id_firma ) AS denumire_firma FROM `aparat` AS aparat
					WHERE aparat.activ='1' AND aparat.id_firma = '".$firma->getID()."'
					ORDER BY aparat.ordinea ASC";

		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL());
		while($aparat = mysql_fetch_array($result))
		{
			echo'
			<tr onclick="document.location='."'"."optiuni_aparat.php?id_aparat=".$aparat['id']."&id_firma=".$aparat['id_firma']."'".'" class="hover" >
			<td >'.$aparat['ordinea'].'</td>
			<td >'.$aparat['serie'].'</td>
			<td>'.$aparat['nume'].'</td>
			<td >'.$aparat['data_autorizatie'].'</td>
			<td >'.$aparat['data_inspectie'].'</td>
			<td >'.$aparat['denumire_firma'].'</td>
			</tr>';
		}

		echo '
		</tbody>
	</table>
</div>';
?>

<script>
$(document).ready(function() {
    $('#example').dataTable({	"bJQueryUI": true,
			"sPaginationType": "full_numbers"});
} );
</script>
<?php

		Page::showFooter();
