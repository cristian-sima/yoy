<?php

require_once "app/Aparat.php";
require_once "app/Aplicatie.php";
require_once "app/FirmaSpatiu.php";

Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->Database;

?>
<div class="container">
	<div class="row">
		<div class="col-xs-8">
			<h1><img src="public/images/user.png" align="absmiddle" />Utilizatori</h1>
		</div>
		<div class="col-xs-4 text-xs-right">
			<input class="btn btn-success btn-sm" type="button" value="Adaugă administrator"	onclick="document.location='adauga_utilizator.php?type=admin'" />
			<input class="btn btn-success btn-sm" type="button" value="Adaugă operator" onclick="document.location='adauga_utilizator.php?type=normal'" />
		</div>
	</div>
</div>
<div class="mt-2">
	<h4>Conturi active</h4>
	<table cellpadding="0" cellspacing="0" border="0" class="display"	id="activeAccounts" style="">
		<thead>
			<tr>
				<th>Utilizator</th>
				<th>Firma</th>
				<th>Tipul</th>
				<th>Opțiuni</th>
			</tr>
		</thead>
		<tbody>
			<?php

			$query = (
				"SELECT utilizator.*,
				(
					SELECT f.nume
					FROM firma AS f
					WHERE f.id=utilizator.idFirma
				) AS denumire_firma
				FROM `utilizator`
				WHERE activ='1'"
			);

			$stmt = $db->prepare($query);
			$ok    = $stmt->execute();

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach ($stmt as $account) {
				echo '
				<tr >
				<td >' . htmlspecialchars($account['nume']) . '(<span class="smoke">' . htmlspecialchars($account['user']) . '</span>)</td>
				<td>';
				if ($account['tipCont'] == "admin") {
					echo 'Toate';
				}
				else {
					echo $account['denumire_firma'];
				}
				echo '</td>
				<td>' . (($account['tipCont'] == "admin") ? "Administrator" : "Operator (" . $account['tipOperator'] . ')') . '</td>
				<td>
				<input class="btn btn-sm btn-info" type="button" value="Modifică datele" onclick="document.location=' . "'" . 'editare_date_utilizator.php?id_user=' . $account['id'] . '' . "'" . '"/>

				<input class="btn btn-sm btn-danger" type="button" value="Dezactivează" onclick="document.location=' . "'" . 'activeaza_utilizator.php?id_user=' . $account['id'] . '&type=0' . "'" . '" />
				</td>
				</tr>';
			}
			?>
		</tbody>
	</table>
</div>
<div class="mt-2">
	<h4>Conturi inactive</h4>
	<table cellpadding="0" cellspacing="0" border="0" class="display"	id="inactiveAccounts" style="">
		<thead>
			<tr>
				<th>Utilizator</th>
				<th>Firma</th>
				<th>Tipul</th>
				<th>Opțiuni</th>
			</tr>
		</thead>
		<tbody>
			<?php

			$query = (
				"SELECT utilizator.*,
				(
					SELECT f.nume
					FROM firma AS f
					WHERE f.id=utilizator.idFirma
				) AS denumire_firma
				FROM `utilizator`
				WHERE activ='0'"
			);

			$stmt = $db->prepare($query);
			$ok    = $stmt->execute();

			if (!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach ($stmt as $account) {
				echo '

				<tr >
				<td >' . htmlspecialchars($account['nume']) . '(<span class="smoke">' . htmlspecialchars($account['user']) . '</span>)</td>
				<td>';
				if ($account['tipCont'] == "admin") {
					echo 'Toate';
				}
				else {
					echo $account['denumire_firma'];
				}
				echo '</td>
				<td>' . (($account['tipCont'] == "admin") ? "Administrator" : "Operator (" . $account['tipOperator'] . ')') . '</td>
				<td>
				<input class="btn btn-sm btn-danger" type="button" value="Activează cont" onclick="document.location = ' . "'" . 'activeaza_utilizator.php?id_user=' . $account['id'] . '&type=1' . "'" . '" />
				</td>
				</tr>';
			}
			?>
		</tbody>
	</table>
</div>
<script>
$(document).ready(function() {
	$('#activeAccounts').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});

	$('#inactiveAccounts').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
});
</script>
<?php
Page::showFooter();
