<?php

require_once "include/php/Aparat.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";

Page::showHeader();
Page::showContent();

echo '
<table id="heading">
	<tr>
		<td>
			<h2 style="color: orange">
				<img src="img/user.png" align="absmiddle" />Utilizatori
			</h2>
		</td>
		<td style="text-align: right">';?><input type="button"
			value="Adaugă administrator"
			onclick="document.location='adauga_utilizator.php?type=admin'" /> <input
			type="button" value="Adaugă operator"
			onclick="document.location='adauga_utilizator.php?type=normal'" />
		<?php echo '
		</td>
	</tr>
</table>
	<div style="width: 958px; margin-left: 13px;">
		<table cellpadding="0" cellspacing="0" border="0" class="display"
			id="example" style="">
			<thead>
				<tr>
					<th>Utilizator</th>
					<th>Firma</th>
					<th>Tipul</th>
					<th>Opțiuni</th>
				</tr>
			</thead>
			<tbody>
			';
			$q = "SELECT utilizator.*,(SELECT f.nume FROM firma AS f WHERE f.id=utilizator.idFirma) AS denumire_firma FROM `utilizator` WHERE activ='1'";

			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($utilizator = mysql_fetch_array($result))
			{
				echo'

				<tr >
				<td >'.htmlspecialchars($utilizator['nume']).'(<span class="smoke">'.htmlspecialchars($utilizator['user']).'</span>)</td>
				<td>';
				if($utilizator['tipCont']=="admin")
					echo'Toate';
				else
					echo $utilizator['denumire_firma'];

				echo'</td>
					<td>'.(($utilizator['tipCont']=="admin")?"Administrator":"Operator (".$utilizator['tipOperator'].')').'</td>
				<td><input type="button" value="Modifică datele" onclick="document.location='."'".'editare_date_utilizator.php?id_user='.$utilizator['id'].''."'".'"/><input type="button" value="Dezactivează" onclick="document.location='."'".'activeaza_utilizator.php?id_user='.$utilizator['id'].'&type=0'."'".'" /></td>
				</tr>';
			}
			echo '
			</tbody>
		</table>
		<br /> <br /> <Br /> Conturi dezactivate<br />
		<table cellpadding="0" cellspacing="0" border="0" class="display"
			id="example2" style="">
			<thead>
				<tr>
					<th>Utilizator</th>
					<th>Firma</th>
					<th>Tipul</th>
					<th>Opțiuni</th>

				</tr>
			</thead>
			<tbody>';

			$q = "SELECT utilizator.*,(SELECT f.nume FROM firma AS f WHERE f.id=utilizator.idFirma) AS denumire_firma FROM `utilizator` WHERE activ='0'";;
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($utilizator = mysql_fetch_array($result))
			{
				echo'

				<tr >
				<td >'.htmlspecialchars($utilizator['nume']).'(<span class="smoke">'.htmlspecialchars($utilizator['user']).'</span>)</td>
				<td>';
				if($utilizator['tipCont']=="admin")
					echo'Toate';
				else
				{
					echo $utilizator['denumire_firma'];
				}
				echo'</td>
				<td>'.(($utilizator['tipCont']=="admin")?"Administrator":"Operator (".$utilizator['tipOperator'].')').'</td>
				<td><input type="button" value="Activează cont" onclick="document.location = '."'".'activeaza_utilizator.php?id_user='.$utilizator['id'].'&type=1'."'".'" /></td>
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

	  $('#example2').dataTable({	"bJQueryUI": true,
					"sPaginationType": "full_numbers"});

} );
</script>
<?php

	Page::showFooter();
