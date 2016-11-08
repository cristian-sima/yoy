<?php

	require_once "include/php/Aparat.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	
	Page::showHeader();
	Page::showContent();
	




		Page::showHeading("Vizualizați toate aparate active si inactive",'<input type="button" value="Adaugati aparat in depozit" onclick="document.location='."'".'adauga_aparat.php'."'".'" />');
		
		echo '
		</td>
	</tr>
</table>

<div style="width: 958px;margin-top:20px; margin-left: 13px;">
	<table cellpadding="0" cellspacing="0" border="0" class="display"
		id="example1" style="">
		<thead>
			<tr>
				<th>Nr</th>
				<th>Seria</th>
				<th>Numele ap.</th>
				<th>Exp. autoriz.</th>
				<th>Exp. insp. tech.</th>
				<th>Denumire firma spatiu</th>
			</tr>
		</thead>
		<tbody>';
			
		$mysql = "SELECT aparat.*,
					(SELECT `nume` FROM `firma` WHERE firma.id=aparat.id_firma )AS denumire_firma FROM `aparat` AS aparat
					WHERE aparat.id_firma != '0' AND aparat.activ='1' 
					ORDER BY aparat.ordinea ASC";

		$result = mysql_query($mysql, Aplicatie::getInstance()->getMYSQL()->getResource());
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
</div>

<br /> Aparate inactive:
<div style="width: 958px;margin-top:20px; margin-left: 13px;">
	<table cellpadding="0" cellspacing="0" border="0" class="display"
		id="example2" style="">
		<thead>
			<tr>
				<th>Nr</th>
				<th>Seria</th>
				<th>Numele ap.</th>
				<th>Exp. autoriz.</th>
				<th>Exp. insp. tech.</th>
				<th>Denumire firma spatiu</th>
			</tr>
		</thead>
		<tbody>';
			
		$q = "SELECT aparat.*,(SELECT `nume` FROM `firma` WHERE firma.id=aparat.id_firma ) AS denumire_firma FROM `aparat` AS aparat
					WHERE aparat.activ='0' 
					ORDER BY aparat.ordinea ASC";

		$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
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
    $('#example1').dataTable({	"bJQueryUI": true,
			"sPaginationType": "full_numbers"});
 

    $('#example2').dataTable({	"bJQueryUI": true,
			"sPaginationType": "full_numbers"});
} );
</script>
<?php

		Page::showFooter();