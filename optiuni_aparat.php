<?php

require_once "include/php/Aparat.php";
require_once 'include/php/Aplicatie.php';
require_once 'include/php/FirmaSpatiu.php';
require_once "include/php/SelectSituatie_GUI.php";

Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->Database;

Page::showHeading("Detalii aparat", "");

$aparat = new Aparat($_GET['id_aparat']);

$query  = (
	"SELECT nume, id, activa
	FROM firma
	WHERE id !=:companyID AND activa = '1'
	ORDER BY activa DESC, nume ASC"
);

$stmt   = $db->prepare($query);
$ok     = $stmt->execute([
	":companyID" => $aparat->getFirmaCurenta()
]);

if (!$ok) {
	throw new Exception("Ceva nu a mers cum trebuia");
}

$noActiveCompanies = ($stmt->rowCount() == 0)

?>

<link href="include/css/fieldset.css" rel="stylesheet" type="text/css"/>

<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="toate_aparatele.php">Aparate</a></li>
  <li class="breadcrumb-item active"><?php echo $aparat->getNume()." ".$aparat->getSerie(); ?></li>
</ol>

<fieldset>
	<legend>Istoric aparat</legend>
	<table width="100%">
		<tr>
			<td class="smoke"> Unitatea</td>
			<td class="smoke"> De la</td>
			<td class="smoke"> Până la</td>
		</tr>
		<?php
		$query = (
			"SELECT *
			FROM istoric_aparat
			WHERE id_aparat=:deviceID
			ORDER BY id"
		);

		$stmt2 = $db->prepare($query);
		$ok    = $stmt2->execute([
			"deviceID" => $aparat->getID()
		]);

		if (!$ok) {
			throw new Exception("Ceva nu a mers cum trebuia");
		}

		foreach ($stmt2 as $deviceHistory) {
			if ($deviceHistory['id_firma'] == '0') {
				$nume_firma = "Depozit";
			} else {
				$firma2     = new FirmaSpatiu($deviceHistory['id_firma']);
				$nume_firma = $firma2->getDenumire();
			}
			echo '	<tr>
			<td class="smoke" style="background: rgb(231, 231, 231);">' . $nume_firma . '</td>
			<td class="smoke" style="background: rgb(231, 231, 231);">' . $deviceHistory['from_'] . '</td>
			<td style="background: rgb(231, 231, 231);" class="smoke" style="background: rgb(231, 231, 231);">' . (($deviceHistory['is_now'] == '1') ? ("În prezent") : ($deviceHistory['to_'])) . '</td>
			</tr>';
		}
		?>
	</table>
</fieldset>
<table width="100%">
	<tr>
		<td style="width:50%">
			<fieldset>
				<legend>Editează date aparat</legend>
				<form action="editeaza_date_aparat.php">
					<input type="hidden" value=<?php
					$aparat->getID();
					?> name="id_aparat" />
					<input  type="submit" value="Editează date" />
				</form>
				<br />
			</fieldset>
		</td>
		<td style="width:50%">
			<fieldset>
				<legend> Mutați aparatul în depozit </legend>
				<form action="muta_aparat_in_depozit.php">
					Mutarea aparatului nu influențează situația de astăzi pentru firma respectivă. Această situație poate să fie completată până la orele 24 astăzi. De mâine, aparatul nu mai apare în firmă
					<br /><br />
					<?php
					echo '
					<input type="hidden" value="' . $aparat->getID() . '" name="id_aparat" />
					<input ' . (($aparat->isActiv()) ? ("") : ("disabled")) . ' ' . (($aparat->isInDepozit()) ? ("disabled") : ("")) . ' type="submit" value="Mută în depozit" />';
					?>
				</form>
				<br />
			</fieldset>
		</td>
	</tr>
	<tr>
		<td style="width:50%">
			<fieldset>
				<legend>Scoate aparatul din uz</legend>
				<br />
				<br />
				<form action="scoate_aparat_din_uz.php">
					Eliminarea aparatului nu influențează situația de astăzi pentru firma respectivă. Această situație poate să fie completată până la orele 24 astăzi. De mâine, aparatul nu mai apare în firmă
					<br />
					<br />
					<?php
					echo '
					<input type="hidden" value="' . $aparat->getID() . '" name="id_aparat" />
					<input ' . (($aparat->isActiv()) ? ("") : ("disabled")) . ' type="submit" value="Scoate din uz" />';
					?>
				</form>
				<br />
			</fieldset>
		</td>
		<td style="width:50%">
			<?php

			if ($noActiveCompanies) {
				echo "Nu există firme active la care să fie mutat aparatul";
			} else {
				?>
				<form action="muta_aparat_la_firma.php" method="POST">
					<fieldset>
						<legend>Mută aparat</legend>
						<br />
						Mutarea aparatului nu influențează situația de astăzi de la firma veche. Această situație poate să fie completată până la ora 24 astăzi. De mâine, aparatul nu mai apare în firmă. Însă, pentru firma nouă, se va crea o nouă situație (sau se modifica cea actuală) și se adaugă aparatul.
						<br />
						<br />
						<?php echo '
						Mută aparatul la firma: <select name="id_firma_noua">';
						foreach ($stmt as $company) {
							echo'<option value="'.$company['id'].'"  >'.($company['nume'])."</option>";
						}
						echo'
						</select>';
						?>
						<br />
						<br />
						<div>
							<b>Contoare:</b>
							<br />
							Index intrări:
							<input type="text"  name="mecanic_intrare" placeholder="Index intrări" />
							<br />
							Index ieșiri:
							<input type="text"  name="mecanic_iesire" placeholder="Index ieșiri" />
							<br />
							<br />
						</div>
						<?php
						echo '
						<input type="hidden" value="'.$aparat->getID().'" name="id_aparat" />
						<input type="hidden" name="id_aparat" value="'.$aparat->getID().'" />
						<input '.(($aparat->isActiv())?(""):("disabled")).' type="submit" value="Mutați aparatul" />';
						?>
					</fieldset>
				</form>
				<?php
			}
			?>
		</td>
	</tr>
</table>
<script>

$(function() {
	$(document).tooltip();
});

$(document).tooltip({
	position: {
		my: "center bottom-20",
		at: "center top",
		using: function(position, feedback) {
			$(this).css(position);
			$("<div>")
			.addClass("arrow")
			.addClass(feedback.vertical)
			.addClass(feedback.horizontal)
			.appendTo(this);
		}
	}
});
</script>';

<?php
Page::showFooter();
