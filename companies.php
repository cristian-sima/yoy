<?php

require_once 'app/Aplicatie.php';

function getActiveCompanies($db) {
	$query = (
		"SELECT *, (
			SELECT valoare
			FROM `procent`
			WHERE `idFirma` = firma.id AND `isNow`= '1'
		) AS currentPercent
		FROM `firma`
		WHERE `activa`= :active"
	);

	$activeCompanies = $db->prepare($query);
	$ok = $activeCompanies->execute(array(
		'active' => "1"
	));

	if(!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	return $activeCompanies;
}

function getInactiveCompanies($db) {
	$query = (
		"SELECT *
		FROM `firma`
		WHERE `activa`= '0'"
	);

	$inactiveCompanies = $db->prepare($query);
	$ok = $inactiveCompanies->execute();

	if(!$ok) {
		throw new Exception("Ceva nu a mers cum trebuia");
	}

	return $inactiveCompanies;
}

function getPercent($percent) {
	if(isset($percent)) {
		return $percent.'%';
	}

	return 'Neprecizat';
}

try {
	Design::showHeader();
	$db = Aplicatie::getInstance()->Database;

	$activeCompanies = getActiveCompanies($db);
	$inactiveCompanies = getInactiveCompanies($db);

	?>
	<div class="container">
		<div class="row">
			<div class="col-xs-9 col-sm-10 col-md-8 h2">
				<span class="hidden-sm-down">
					<img src="public/images/firme.png" alt="Firme partenere" />
				</span>
				Firme partenere
			</div>
			<div class="col-xs-3 col-sm-2 col-md-4 text-xs-right">
				<a class="btn btn-success" href="add_company.php"	>
					<i class="fa fa-plus"></i>
					<span class="hidden-sm-down">
						Adaugă firmă
					</span>
				</a>
			</div>
		</div>
		<hr>
		<div class="mt-2">
			<h4>Firme active</h4>
			<table class="display"	id="active-companies-table">
				<thead>
					<tr>
						<th>Denumire</th>
						<th>Localitate</th>
						<th>Procent</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($activeCompanies as $company) {
						?>
						<tr>
							<td class="no-wrap">
								<a href="company_details.php?id=<?= $company["id"]; ?>">
									<?= $company["nume"]; ?>
								</a>
							</td>
							<td><?= $company["localitate"]; ?></td>
							<td><?= getPercent($company["currentPercent"]) ?></td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<hr>
		<div class="mt-2">
			<h4>Firme inactive</h4>
			<table class="display" id="inactive-companies-table">
				<thead>
					<tr>
						<th>Denumire</th>
						<th>Localitate</th>
						<th class="no-wrap">Dată încetare</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($inactiveCompanies as $company) {
						?>
						<tr>
							<td class="no-wrap">
								<a href="company_details.php?id=<?= $company["id"]; ?>">
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
	
	<?=	DESIGN::showFooter();	?>

	<script type="text/javascript">
	(function() {
		$('#active-companies-table, #inactive-companies-table').dataTable();
	})();
	</script>

	<?php
} catch (Exception $e) {
	DESIGN::complain($e->getMessage());
}
