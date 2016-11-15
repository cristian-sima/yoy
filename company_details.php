<?php

require_once "app/func/calendar.php";

require_once "app/Aplicatie.php";
require_once "app/FirmaSpatiu.php";
require_once "app/SituatieMecanica.php";

function getDevicesForCompany ($db, $companyID) {
	$query      = (
		"SELECT *
		FROM `aparat`
		WHERE activ='1' AND id_firma=:companyID
		ORDER BY ordinea ASC"
	);

	$devices = $db->prepare($query);
	$ok = $devices->execute(array(
		"companyID" => $companyID,
	));

	if(!$ok) {
		throw new Exception("Ceva nu a mers așa cum trebuia");
	}

	return $devices;
}

function getAccountsForCompany ($db, $companyID) {
	$query      = (
		"SELECT *
		FROM `utilizator`
		WHERE activ='1' AND idFirma=:companyID
		ORDER BY nume ASC"
	);

	$users = $db->prepare($query);
	$ok = $users->execute(array(
		"companyID" => $companyID
	));

	if(!$ok) {
		throw new Exception("Ceva nu a mers așa cum trebuia");
	}

	return $users;
}

function getStatus($isActive) {
	$type = $isActive ? "success" : "danger";
	$text = $isActive ? "contract activ" : "contract încetat";
	?>
	<span class="tag tag-<?= $type ?>"><?= $text ?></span>
	<?php
}

function getUserType($type) {
	if ($type == "admin") {
		?>
		<span class="tag tag-info">Administrator</span>
		<?php
		return "";
	}

	?>
	<span class="tag tag-default">Operator</span>
	<?php
}

function getLastCompletion($company, $date) {
	$ultima_data = SituatieMecanica::getUltimaCompletare($company, $date);

	if ($ultima_data == null) {
		return "Situația nu a fost completată niciodată";
	}

	$now         = time();
	$datediff    = $now - strtotime($ultima_data);
	$dif         = (floor($datediff / (60 * 60 * 24)));
	$when = (($dif == 1) ? "ieri" : (($dif == 0) ? "astăzi" : ($dif . " zile" . '  în urmă')));

	return "Ultima situație completată a fost ".$when;
}

try {

	$db =  Aplicatie::getInstance()->Database;
	$company       = new FirmaSpatiu($_GET['id']);
	$date        = new DataCalendaristica(date("Y-m-d"));
	$companyID = $company->getID();
	$devices = getDevicesForCompany($db, $companyID);
	$accounts = getAccountsForCompany($db, $companyID);

	Design::showHeader();
	$db = Aplicatie::getInstance()->Database;

	?>
	<div class="container">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="companies.php">Firme partenere</a></li>
			<li class="breadcrumb-item active"><?php echo $company->getDenumire(); ?></li>
		</ol>
		<div class="card">
			<div class="card-block">
				<h4 class="card-title">Date generale</h4>

				<div class="card-text">
					<div class="container">
						<div class="row">
							<div class="col-md-6">
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-6">
											Denumire
										</div>
										<div class="col-xs-6">
											<?php echo $company->getDenumire(); ?>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6">
											Localitate
										</div>
										<div class="col-xs-6">
											<?php echo $company->getLocatie(); ?>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6">
											Statut firmă
										</div>
										<div class="col-xs-6">
											<?= getStatus($company->isActiva()); ?>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-6">
											Procent
										</div>
										<div class="col-xs-6">
											<?php	echo $company->getProcentFirma($date); ?> %
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 text-md-right text-xs-center mt-2">
								<button type="button" class="btn btn-info btn-lg" onClick="document.location='situatie_mecanica.php?id_firma=<?php	echo $companyID;	?>'" >
									Situație zilnică
								</button>
								<br />
								<span	class="text-muted small">
									<?= getLastCompletion($company, $date) ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-block">
				<h4 class="card-title">Situații</h4>

				<div class="card-text">
					<div class="container">
						<div class="row">
							<div class="col-md-6">
								<form class="form-inline">
									<div class="form-group">
										<label for="year">Anul</label>
										<select name="year" class="custom-select" id="year">
											<?php
											for ($year = $yearStart; $year <= $yearEnd; $year++) {
												?>
												<option value="<?= $year ?>" <?= (($year == $date->getAnul()) ? ("selected") : "") ?>><?= $year ?></option>
												<?php
											}
											?>
										</select>
									</div>
									<div class="form-group">
										<label for="month">Luna</label>
										<select name="month" class="custom-select" id="month">
											<?php
											for ($month = 1; $month <= 12; $month++) {
												?>
												<option value="<?= $month ?>" <?= (($month == $date->getLuna()) ? ("selected") : "") ?>><?= getMonthName($month) ?></option>
												<?php
											}
											?>
										</select>
									</div>
								</form>
								<div class="pt-1 text-xs-center text-md-left">
									<a class="btn btn-info btn-sm" onclick="seeData('vizualizare_dispozitii')" href="#">Dispoziții</a>
									<a class="btn btn-info btn-sm" onclick="seeData('registru_firma_spatiu')" href="#">Registru firmă</a>
									<a class="btn btn-info btn-sm" onclick="seeData('afisare_decont_firma')" href="#">Decont</a>
								</div>
							</div>
							<div class="col-md-6 text-md-right text-xs-center mt-2">
								<?php
								if ($company->isActiva()) {
									?>
									<a class="btn btn-info btn-sm" onClick="seeData('inchide_situatie_luna')" href="#">
										Închide lună
									</a>
									<?php
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-block">
				<h4 class="card-title">Aparate</h4>
				<div class="card-text">
					<div class="clearfix mb-1">
						<?php
						if ($company->isActiva() != '0') {
							?>
							<a class="btn btn-success btn-sm float-xs-right" href="adauga_aparat.php?id_firma=<?= $companyID;	?>">
								Adaugă aparat
							</a>
							<?php
						}
						?>
					</div>
					<table class="display"	id="devices-table">
						<thead>
							<tr>
								<th>Nr</th>
								<th>Seria</th>
								<th>Denumirea</th>
								<th class="no-wrap">Exp. autoriz.</th>
								<th class="no-wrap">Exp. insp. tech.</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($devices as $device) {
								?>
								<tr>
									<td>
										<?= $device["ordinea"] ?>
									</td>
									<td>
										<a href="optiuni_aparat.php?situatie=true<?= "&" ?>id_aparat=<?= $device["id"]; ?>">
											<?= $device["serie"]; ?>
										</a>
									</td>
									<td><?= $device["nume"]; ?></td>
									<td class="text-xs-center no-wrap">
										<?= localeDate($device["data_autorizatie"]) ?>
									</td>
									<td class="text-xs-center no-wrap">
										<?= localeDate($device["data_inspectie"]) ?>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-block">
				<h4 class="card-title">Conturi care au access</h4>
				<div class="card-text">
					<div class="clearfix mb-1">
						<?php
						if ($company->isActiva() != '0') {
							?>
							<div class="float-sm-right text-xs-center">
								<a class="btn btn-success btn-sm" href="adauga_utilizator.php?type=admin">
									Adaugă administrator
								</a>
								<a class="btn btn-success btn-sm" href="adauga_utilizator.php?type=normal">
									Adaugă operator
								</a>
							</div>
							<?php
						}
						?>
					</div>
					<table class="display"	id="users-table">
						<thead>
							<tr>
								<th class="no-wrap">Nume și prenume</th>
								<th class="no-wrap">Utilizator</th>
								<th>Tipul</th>
								<th>Opțiuni</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($accounts as $account) {
								?>
								<tr>
									<td>
										<?= $account["nume"] ?>
									</td>
									<td style="font-family: courier">
										<?= $account["user"] ?>
									</td>
									<td>
										<?= getUserType($account["tipCont"]) ?>
									</td>
									<td>
										<div class="no-wrap">
											<a class="btn btn-link" href="editare_date_utilizator.php?id_user=<?= $account["id"] ?>">
												Modifică datele
											</a>
											<a class="btn btn-link" href="activeaza_utilizator.php?id_user=?id_user=<?= $account["id"] ?><?= '&' ?>type=0">
												Dezactivează
											</a>
										</div>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="card">
			<div class="card-block">
				<h4 class="card-title">Alte operațiuni</h4>
				<hr>
				<div class="card-text">
					<a class="btn btn-primary btn-sm" href="editare_date_firma.php?id=<?= $companyID ?>">
						Modifică informațiile
					</a>
				</div>
			</div>
		</div>

	</div>

	<?=	DESIGN::showFooter();	?>

	<script type="text/javascript">

	function seeData(where) {
		document.location = where + ".php?id_firma=<?= $companyID; ?>&data="+$("#year").val()+"-"+$("#month").val()+"-01";
	}

	(function() {
		$('#devices-table, #users-table').dataTable();
	})();

	</script>

	<?php
} catch (Exception $e) {
	DESIGN::complain($e->getMessage());
}
