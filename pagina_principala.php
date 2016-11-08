<?php

	require_once 'include/php/Aplicatie.php';

	Page::showHeader();
	Page::showContent();

?>

	<table id="heading">
		<tr>
			<td>
				<h2 style="color: orange">
					<img src="img/firme.png" align="absmiddle" />Firme partenere spațiu
				</h2>
			</td>
			<td style="text-align: right"><input type="button" value="Firmă nouă"
				onclick="document.location='firma_noua.php'" />
			</td>
		</tr>
	</table>
	<div class="big_table">
		<table cellpadding="0" cellspacing="0" border="0" class="display"
			id="example" style="">
			<thead>
				<tr>
					<th>Nume firma</th>
					<th>Localitate</th>
					<th>Procent</th>

				</tr>
			</thead>
			<tbody>
			<?php
			$q = "SELECT * from `firma` WHERE `activa`='1'";
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($row = mysql_fetch_array($result))
			{
				echo'

			<tr onclick="document.location='."'"."details.php?idFirma=".$row['id']."'".'" class="hover">
				<td >'.$row['nume'].'</td>
				<td>'.$row['localitate'].'</td>
				<td>';
				$q2 = "SELECT * from procent WHERE `idFirma`='".$row['id']."' AND isNow='1' limit 0,1";
				$result2 = mysql_query($q2, Aplicatie::getInstance()->getMYSQL()->getResource());
				while($row2 = mysql_fetch_array($result2))
				{
					echo $row2['valoare'];
				}

				echo'</td>



				</tr>';
		 }?>
			</tbody>
		</table>

		<br /> <br /> Firme inactive (contracte terminate)<br /> <br />

		<table cellpadding="0" cellspacing="0" border="0" class="display"
			id="example2" style="">
			<thead>
				<tr>
					<th>Nume firma</th>
					<th>Localitate</th>
					<th>Data incetare</th>

				</tr>
			</thead>
			<tbody>
			<?php
			$q = "SELECT * from `firma` WHERE `activa`='0'";
			$result = mysql_query($q, Aplicatie::getInstance()->getMYSQL()->getResource());
			while($row = mysql_fetch_array($result))
			{
				echo'
				<tr onclick="document.location='."'"."details.php?idFirma=".$row['id']."'".'" class="hover">
				<td >'.$row['nume'].'</td>
				<td>'.$row['localitate'].'</td>
				<td>'.$row['dataIncetare'].'</td>
				</tr>';
		 }?>
			</tbody>
		</table>
	</div>

	<script>
	$(document).ready(function() {
	    $('#example').dataTable({	"bJQueryUI": true,
						"sPaginationType": "full_numbers"});
		$('#example2').dataTable({	"bJQueryUI": true,
						"sPaginationType": "full_numbers"});
	} );
	</script>

		 <?php

		 PAGE::showFooter();

		 ?>
