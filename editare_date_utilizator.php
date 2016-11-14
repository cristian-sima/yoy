<?php

require_once "include/php/Utilizator.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";

Login::permiteOperator();
Page::showHeader();
Page::showContent();

$db = Aplicatie::getInstance()->Database;

if (Aplicatie::getInstance()->getUtilizator()->isOperator()) {
  $utilizator = Aplicatie::getInstance()->getUtilizator();
} else {
  $utilizator = new Utilizator($db, $_GET['id_user']);
}

echo '
	<table id="heading">
		<tr>
			<td></td>
			<td style="text-align: right">';
if (Aplicatie::getInstance()->getUtilizator()->isAdministrator()) {
  echo ' <a href="utilizatori.php"><input type="button" value="Înapoi la utilizatori" /></a>
	&nbsp; &nbsp; &nbsp; &nbsp;
 	<a href="reseteaza_parola.php?id_user=' . $utilizator->getID() . '"><input type="button" value="Resetează parola" /> </a>';
} else {
  echo '<a href="index.php" ><input 	type="button" value="Înapoi" />';
}
echo '
			</td>
		</tr>
	</table>

	<div class="content">
		<div id="dialog"></div>
		<div id="form_wrapper" class="form_wrapper"
			style="width: 550px; display: block;">
			<form id="f1" class="register active" style=""
				action="
					editare_date_utilizator_POST.php" method="POST">
				<input type="hidden" name="id_user" value="' . $utilizator->getID() . '" />
				<h3>
					<cufon class="cufon cufon-canvas" alt="Register"
						style="width: 106px; height: 25px;"> <canvas width="122"
						height="28"
						style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
					<cufontext> Modifică datele personale</cufontext></cufon>
				</h3>';
?>
<div class="column">
	<div>
		<label>Nume și prenume:</label> <input check="true"
			criteria='{type:"string",  minSize: "5", maxSize:"30"}' name="nume"
			type="text" value="<?php
echo $utilizator->getNume();
?>" id="nume">

	</div>
	<div>
		<label>Utilizator:</label> <input
			value="<?php
echo $utilizator->getUtilizator();
?>" id="user"
			name="user" check="true"
			criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">

	</div>
	<div>
		<label>Parolă:</label>
		<input check="true" criteria='{type:"string", reg:"(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$",  minSize: "8", empty:"true"}' id="parola" value="" name="parola" type="password">

		<small><i>Lăsă câmpul gol dacă nu vrei să modifici parola</i></small>
	</div>
	<?php
if ($utilizator->isAdministrator()) {
  echo '<div>
			<div id="idFirma" style="margin-left: 20px;">
				<label>Firma</label> <select name="idFirma">';
  $result = mysql_query("SELECT id,nume FROM firma WHERE activa='1'", $db);
  while ($firma = mysql_fetch_array($result)) {
    echo '<option
					' . (($row2['id'] == $user['idFirma']) ? "selected='selected'" : "") . '
						value="' . $firma['id'] . '">' . $firma['nume'] . '</option>';
  }
  echo '
				</select>
			</div>
		</div>';
} else
  echo '<input type="hidden" name="idFirma" value="' . $utilizator->getIDFirma() . '"/>';
echo '
	</div>
	<div class="bottom">
		<div class="remember"></div>';
?>
	<input type="button" class="button" onclick="checkForm('f1')"
		value="Modifică date">
		<?php
if (Aplicatie::getInstance()->getUtilizator()->isOperator()) {
  echo '<a href="#" rel="login" class="linkform"> !!!! Dupa ce schimbati parola sau Utilizatorul-ul, o sa fi-ti rugati sa va conectati din nou</a>';
}
echo '
	<div class="clear"></div>
</div>
</form></div></div>';
?>
	<div class="clear"></div>
<?php
Page::showFooter();
