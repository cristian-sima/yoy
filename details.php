<?php

require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";
require_once "include/php/SituatieMecanica.php";

require_once "vendor/StringTemplate/Engine.php";
$engine = new Engine();


Page::showHeader();
Page::showContent();

$db =  Aplicatie::getInstance()->Database;

$firma       = new FirmaSpatiu($_GET['idFirma']);
$data        = new DataCalendaristica(date("Y-m-d"));
$ultima_data = SituatieMecanica::getUltimaCompletare($firma, $data);

if ($ultima_data == null) {
	$ultima_data = "Niciodată";
	$zileTrecute = "";
} else {
	$now         = time();
	$datediff    = $now - strtotime($ultima_data);
	$dif         = (floor($datediff / (60 * 60 * 24)));
	$zileTrecute = '( ' . (($dif == 1) ? "o zi în urmă" : (($dif == 0) ? "Astăzi" : ($dif . " zile" . '  în urmă'))) . ' )';
}

// devices

$query      = (
	"SELECT *
	FROM `aparat`
	WHERE activ='1' AND id_firma=:companyID
	ORDER BY ordinea ASC"
);

$devices = $db->prepare($query);
$ok = $devices->execute(array(
	"companyID" => $firma->getID()
));

if(!$ok) {
	throw new Exception("Ceva nu a mers așa cum trebuia");
}

// users

$query      = (
	"SELECT *
	FROM `utilizator`
	WHERE activ='1' AND idFirma=:companyID
	ORDER BY nume ASC"
);

$users = $db->prepare($query);
$ok = $users->execute(array(
	"companyID" => $_GET['idFirma']
));

if(!$ok) {
	throw new Exception("Ceva nu a mers așa cum trebuia");
}

?>

<link href="include/css/butoane_pacanele.css" rel="stylesheet" type="text/css" />
<link href="include/css/fieldset.css" rel="stylesheet" type="text/css"/>
<fieldset>
	<legend>
		<?php
		echo '<span class="bold" style="color:orange">' . $firma->getDenumire() . '</span>';
		?>
	</legend>
	<table width="100%">
		<tr>
			<td width="50%">
				<table width="300px">
					<tr>
						<td width="50%" class="smoke">Localitate</td>
						<td width="50%">
							<?php
							echo '<span class="bold" style="color:gray">' . $firma->getLocatie() . '</span>';
							?>
						</td>
					</tr>
					<tr>
						<td width="50%" class="smoke">Statut firmă</td>
						<td width="50%">
							<?php
							$color = (($firma->isActiva()) ? "green" : "red");
							echo '<span style="color:' . $color . '">' . (($firma->isActiva()) ? "contract activ" : "contract încetat") . "
							</span>";
							?>
						</td>
					</tr>
					<tr>
						<td width="50%" class="smoke">Procent</td>
						<td width="50%">
							<?php	echo $firma->getProcentFirma($data); ?> %
						</td>
					</tr>
				</table>
			</td>
			<td width="50%" style="text-align:right">
				<a onclick="document.location='situatie_mecanica.php?id_firma=<?php	echo $firma->getID();	?>'" class="button green medium">
					Situație zilnică
				</a>
				<br />
				<span	class="smoke" style="font-size: 12px">
					Ultima completare: <?php echo $ultima_data . ' ' . $zileTrecute; ?>
				</span>
			</td>
		</tr>
	</table>
</fieldset>
<fieldset>
	<legend class="bold">Situații</legend>
	<table width="100%">
		<tr>
			<td width="50%" class="smoke">
				<?php
				echo 'Anul <select id="an">';
				for ($i = 2013; $i <= 2020; $i++) {
					echo '<option value="' . $i . '" ' . (($i == $data->getAnul()) ? ("selected") : "") . '>' . $i . "</option>";
				}
				echo "</select>";
				echo '&nbsp;&nbsp;&nbsp;&nbsp;Luna <select id="luna">';
				for ($luna = 1; $luna <= 12; $luna++) {
					echo '<option value="' . $luna . '" ' . (($luna == $data->getLuna()) ? ("selected") : "") . ' >' . DataCalendaristica::getNumeleLunii($luna) . "</option>";
				}
				echo "</select>";
				?>
			</td>
			<td width="50%" style="text-align: right">
				<?php
				if ($firma->isActiva()) {
					?>
					<a onclick="seeData('inchide_situatie_luna')" href="#" class="button orange small bold">
						Închidere lună
					</a>
					<a onclick="seeData('editare_date_firma')" href="#" class="button orange small bold">
						Modificare date firmă
					</a>
					<?php
				}
				?>
			</td>
		</tr>
	</table>

	<br />
	<br />

	<a onclick="seeData('vizualizare_dispozitii')" href="#" class="button blue small bold">Dispoziții</a>
	<a onclick="seeData('registru_firma_spatiu')" href="#" class="button gray small bold">Registru firmă</a>
	<a onclick="seeData('afisare_decont_firma')" href="#" class="button blue small bold">Decont</a>

</fieldset>

<fieldset>

	<legend class="bold">Aparate</legend>

	<table width="100%">
		<tr>
			<td>
				<?php
				if ($firma->isActiva() != '0') {
					?>
					<input type="button" value="Adaugă aparat" onclick="document.location='adauga_aparat.php?id_firma=<?php	echo $firma->getID();	?>'" />
					<?php
				}
				?>
			</td>
			<td style="text-align: right"></td>
		</tr>
	</table>
	<br />
	<div style=" margin-left: 13px;">
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="example" style="">
			<thead>
				<tr>
					<th>Nr</th>
					<th>Seria</th>
					<th>Denumirea</th>
					<th>Exp. autoriz.</th>
					<th>Exp. insp. tech.</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($devices as $device) {
					$result = $engine->render(
						'<tr onclick=document.location="{url}" class="hover" >
						<td>{order}</td>
						<td>{serial}</td>
						<td>{name}</td>
						<td>{autorizationDate}</td>
						<td>{inspectionDate}</td>
						</tr>',
						[
							"companyID" => $device['id_firma'],
							"url" => 'optiuni_aparat.php?situatie=true&id_aparat='.$device['id'],
							"order" => $device['ordinea'],
							"serial" => $device['serie'],
							"name" => $device['nume'],
							"autorizationDate" => $device['data_autorizatie'],
							"inspectionDate" => $device['data_inspectie']
						]
					);

					echo $result;
				}
				?>
			</tbody>
		</table>
	</div>
</fieldset>
<fieldset>
	<legend class="bold">Operatori</legend>
	<table width="100%">
		<tr>
			<td><?php
			if ($firma->isActiva()) {
				?> <input type="button"
				value="Adaugă administrator"
				onclick="document.location='adauga_utilizator.php?type=admin'" /> <input
				type="button" value="Adaugă operator"
				onclick="document.location='adauga_utilizator.php?type=normal'" /> <?php
			}
			?>
		</td>
		<td style="text-align: right"></td>
	</tr>
</table>
<br />
<div style=" margin-left: 13px;">
	<table cellpadding="0" cellspacing="0" border="0" class="display"	id="example2" style="">
		<thead>
			<tr>
				<th>Nume și prenume</th>
				<th>Utilizator</th>
				<th>Tipul</th>
				<th>Opțiuni</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($users as $user) {
				$result = $engine->render(
					'<tr>
					<td>{name}</td>
					<td>{username}</td>
					<td>{type}</td>
					<td>
						<input type="button" value="Modifică datele" onclick="document.location=' . "'" . 'editare_date_utilizator.php?id_user={userID}' . "'" . '"/>
						<input type="button" value="Dezactivează" onclick="document.location=' . "'" . 'activeaza_utilizator.php?id_user={userID}&type=0' . "'" . '" />
					</td>
					</tr>',
					[
						"name" => $user['nume'],
						"username" => $user['user'],
						"type" =>  ($user['tipCont'] == "admin") ? "Administrator" : "Operator (" . $user['tipOperator'] . ')',
						"userID" => $user['id'],
					]
				);

				echo $result;
			}
			?>
		</tbody>
	</table>
</div>

</fieldset>

<script>
$(document).ready(function() {
	$('#example').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
});

$(document).ready(function() {
	$('#example2').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers"
	});
});

function seeData(where) {
	document.location = where + ".php?id_firma=<?php echo $firma->getID(); ?>&data="+$("#an").val()+"-"+$("#luna").val()+"-01";
}

</script>

<?php
Page::showFooter();
