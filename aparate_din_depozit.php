<?php

	require_once "include/php/Aparat.php";
	require_once "include/php/Aplicatie.php";
	require_once "include/php/FirmaSpatiu.php";
	
	Page::showHeader();
	Page::showContent();
	


echo '
<table id="heading">
	<tr>
		<td>Vizualizați aparatele din depozit
		</b>
		</td>
		<td style="text-align: right">
		'; 
		?><input type="button" value="Adăugați aparat în depozit"
			onclick="document.location='adauga_aparat.php'" />
		<?php	 
		echo '
		</td>
	</tr>
</table>

<div style="width: 958px;margin-top:20px; margin-left: 13px;">
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
		<tbody>';
			
		$q = "SELECT aparat.* FROM `aparat` AS aparat
					WHERE aparat.id_firma='0' 
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