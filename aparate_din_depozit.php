<?php

require_once "app/Aparat.php";
require_once "app/Aplicatie.php";
require_once "app/FirmaSpatiu.php";

Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->Database;

?>
<table id="heading">
	<tr>
		<td>
			Vizualizați aparatele din depozit
		</td>
		<td style="text-align: right">
			<input type="button" value="Adaugă aparat în depozit"	onclick="document.location='adauga_aparat.php'" />
		</td>
	</tr>
</table>

<div style="width: 958px;margin-top:20px; margin-left: 13px;">
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" style="">
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

			$query = (
				"SELECT aparat.*
				FROM `aparat` AS aparat
				WHERE aparat.id_firma='0'
				ORDER BY aparat.ordinea ASC"
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute();

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach ($stmt as $device) {
				echo'
				<tr onclick="document.location='."'"."optiuni_aparat.php?id_aparat=".$device['id']."&id_firma=".$device['id_firma']."'".'" class="hover" >
				<td >'.$device['ordinea'].'</td>
				<td >'.$device['serie'].'</td>
				<td>'.$device['nume'].'</td>
				<td >'.$device['data_autorizatie'].'</td>
				<td >'.$device['data_inspectie'].'</td>
				</tr>';
			}

			?>
		</tbody>
	</table>
</div>
<script>
$(document).ready(function() {
	$('#example').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
});
</script>

<?php

Page::showFooter();
