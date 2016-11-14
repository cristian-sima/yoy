<?php

require_once "include/php/Aparat.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";

Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->Database;

?>
<div class="container">
	<div class="row">
		<div class="col-xs-8">
			<h1>Toate aparatele</h1>
		</div>
		<div class="col-xs-4 text-xs-right">
			<?php
			echo '<input class="btn btn-success" type="button" value="Adaugă aparat în depozit" onclick="document.location=' . "'" . 'adauga_aparat.php' . "'" . '" />';
			?>
		</div>
	</div>
</div>
<div class="mt-2">
	<h4>Aparate în uz</h4>
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="example1" style="">
		<thead>
			<tr>
				<th>Nr</th>
				<th>Seria</th>
				<th>Numele ap.</th>
				<th>Exp. autoriz.</th>
				<th>Exp. insp. tech.</th>
				<th>DENUMIRE FIRMĂ spatiu</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$query  = (
				"SELECT aparat.*,
				(
					SELECT `nume`
					FROM `firma`
					WHERE firma.id=aparat.id_firma
				) AS denumire_firma
				FROM `aparat` AS aparat
				WHERE aparat.id_firma != '0' AND aparat.activ='1'
				ORDER BY aparat.ordinea ASC"
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute();

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach ($stmt as $device) {
				echo '
				<tr onclick="document.location=' . "'" . "optiuni_aparat.php?id_aparat=" . $device['id'] . "&id_firma=" . $device['id_firma'] . "'" . '" class="hover" >
				<td >' . $device['ordinea'] . '</td>
				<td >' . $device['serie'] . '</td>
				<td>' . $device['nume'] . '</td>
				<td >' . $device['data_autorizatie'] . '</td>
				<td >' . $device['data_inspectie'] . '</td>
				<td >' . $device['denumire_firma'] . '</td>
				</tr>';
			}
			?>
		</tbody>
	</table>
</div>
<div class="mt-2">
	<h4>Aparate inactive</h4>
	<table cellpadding="0" cellspacing="0" border="0" class="display"	id="example2" style="">
		<thead>
			<tr>
				<th>Nr</th>
				<th>Seria</th>
				<th>Numele ap.</th>
				<th>Exp. autoriz.</th>
				<th>Exp. insp. tech.</th>
				<th>DENUMIRE FIRMĂ spatiu</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$query      = (
				"SELECT aparat.*,
				(
					SELECT `nume`
					FROM `firma`
					WHERE firma.id=aparat.id_firma
				) AS denumire_firma
				FROM `aparat` AS aparat
				WHERE aparat.activ='0'
				ORDER BY aparat.ordinea ASC"
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute();

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach ($stmt as $device) {
				echo '
				<tr onclick="document.location=' . "'" . "optiuni_aparat.php?id_aparat=" . $device['id'] . "&id_firma=" . $device['id_firma'] . "'" . '" class="hover" >
				<td >' . $device['ordinea'] . '</td>
				<td >' . $device['serie'] . '</td>
				<td>' . $device['nume'] . '</td>
				<td >' . $device['data_autorizatie'] . '</td>
				<td >' . $device['data_inspectie'] . '</td>
				<td >' . $device['denumire_firma'] . '</td>
				</tr>';
			}
			?>
		</tbody>
	</table>
</div>
<script>
$(document).ready(function() {
	$('#example1').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});

	$('#example2').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
});
</script>
<?php
Page::showFooter();
