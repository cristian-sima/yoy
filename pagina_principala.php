<?php

require_once "vendor/StringTemplate/Engine.php";
$engine = new Engine();

require_once 'include/php/Aplicatie.php';

Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->getMYSQL();

?>

<table id="heading">
	<tr>
		<td>
			<h2 style="color: orange">
				<img src="img/firme.png" align="absmiddle" />Firme partenere spațiu
			</h2>
		</td>
		<td style="text-align: right">
			<input type="button" value="Adaugă firmă"	onclick="document.location='firma_noua.php'" />
		</td>
	</tr>
</table>
<div class="big_table">
	<table cellpadding="0" cellspacing="0" border="0" class="display"	id="active_companies" style="">
		<thead>
			<tr>
				<th>Denumire</th>
				<th>Localitate</th>
				<th>Procent</th>
			</tr>
		</thead>
		<tbody>
			<?php

			$query = (
				"SELECT *
				FROM `firma`
				WHERE `activa`= :active"
			);

			$stmt = $db->prepare($query);
			$ok = $stmt->execute(array(
				'active' => "1"
			));

			if(!$ok) {
				throw new Exception("Ceva nu a mers cum trebuia");
			}

			foreach($stmt as $company) {


				$query2 = (
					"SELECT *
					FROM `procent`
					WHERE `idFirma`= :companyID AND isNow=:isNow
					LIMIT 0,1"
				);

				$stmt2 = $db->prepare($query2);
				$ok2 = $stmt2->execute(array(
					'companyID' => $company['id'],
					'isNow' => 1
				));

				if(!$ok2) {
					throw new Exception("Ceva nu a mers cum trebuia");
				}

				$currentProcent = 'Neprecizat';

				foreach($stmt2 as $procent) {
					echo $procent['valoare'];
				}

				$result = $engine->render(
					'<tr onClick=document.location="{url}" class="hover">
					<td>{name}</td>
					<td>{address}</td>
					<td>{currentProcent}</td>
					</tr>
					',
					[
						"url" => 'details.php?idFirma='.$company['id'],
						"name" => $company['nume'],
						"address" => $company['localitate'],
						"currentProcent" => $currentProcent
					]
				);

				echo $result;
			}
			?>
		</tbody>
	</table>
	<br />
	<br />
	Firme inactive (contracte terminate)
	<br />
	<br />
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="inactive_companies" style="">
		<thead>
			<tr>
				<th>Denumire</th>
				<th>Localitate</th>
				<th>Dată încetare</th>
			</tr>
		</thead>
		<tbody>
			<?php

			$q      = "SELECT * from `firma` WHERE `activa`='0'";
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL());

			while ($row = mysql_fetch_array($result)) {
				echo '
				<tr onclick="document.location=' . "'" . "details.php?idFirma=" . $row['id'] . "'" . '" class="hover">
				<td >' . $row['nume'] . '</td>
				<td>' . $row['localitate'] . '</td>
				<td>' . $row['dataIncetare'] . '</td>
				</tr>';
			}
			?>
		</tbody>
	</table>
</div>

<script>
$(document).ready(function() {
	$('#active_companies').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
	$('#inactive_companies').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
});
</script>

<?php
PAGE::showFooter();
?>
