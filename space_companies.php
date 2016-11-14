<?php

require_once 'app/Aplicatie.php';

Design::showHeader();

$db = Aplicatie::getInstance()->Database;

?>

<div class="container">
	<div class="row">
		<div class="col-xs-10 h1">
			<span class="hidden-md-down">
				<img src="public/images/firme.png" alt="Firme spațiu" />
			</span>
			Firme spațiu
		</div>
		<div class="col-xs-2 text-xs-right">
			<a class="btn btn-success" href="firma_noua.php"	>
				<i class="fa fa-plus"></i>
				<span class="hidden-md-down">
					Adaugă firmă
				</span>
			</a>
		</div>
	</div>
	<hr>

	<div class="mt-2">
		<h4>Firme active</h4>
		<table class="display"	id="active_companies">
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

				foreach($stmt as $activeCompany) {

					$query2 = (
						"SELECT *
						FROM `procent`
						WHERE `idFirma`= :companyID AND isNow=:isNow
						LIMIT 0,1"
					);

					$stmt2 = $db->prepare($query2);
					$ok2 = $stmt2->execute(array(
						'companyID' => $activeCompany['id'],
						'isNow' => 1
					));

					if(!$ok2) {
						throw new Exception("Ceva nu a mers cum trebuia");
					}

					$currentProcent = 'Neprecizat';

					foreach($stmt2 as $procent) {
						$currentProcent = $procent["valoare"];
					}

					?>
					<tr>
						<td>
							<a href="details.php?idFirma=<?= $activeCompany["id"]; ?>">
								<?= $activeCompany["nume"]; ?>
							</a>
						</td>
						<td><?= $activeCompany["localitate"]; ?></td>
						<td><?= $currentProcent; ?>% </td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<hr>
	<div class="mt-2">
		<h4>Firme inactive (contracte terminate)</h4>
		<table class="display" id="inactive_companies">
			<thead>
				<tr>
					<th>Denumire</th>
					<th>Localitate</th>
					<th>Dată încetare</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$query = (
					"SELECT *
					FROM `firma`
					WHERE `activa`= '0'"
				);

				$stmt = $db->prepare($query);
				$ok = $stmt->execute();

				if(!$ok) {
					throw new Exception("Ceva nu a mers cum trebuia");
				}

				foreach($stmt as $company) {
					?>
					<tr>
						<td>
							<a href="details.php?idFirma=<?= $company["id"]; ?>">
								<?= $company["nume"]; ?>
							</a>
						</td>
						<td><?= $company["localitate"]; ?></td>
						<td><?= $company["dataIncetare"]; ?></td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>
</div>

<?php
DESIGN::showFooter();
?>

<script type="text/javascript">
(function() {
	$('#inactive_companies, #active_companies').dataTable();
})();
</script>
