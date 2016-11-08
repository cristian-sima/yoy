<?php

require_once "include/php/Aparat.php";
require_once "include/php/Aplicatie.php";
require_once "include/php/FirmaSpatiu.php";

Page::showHeader();
Page::showContent();


?>
<table id="heading">
	<tr>
		<td>Adaugă utilizator</td>
		<td style="text-align: right"><input type="button"
			value="Înapoi la utilizatori"
			onclick="document.location='utilizatori.php'" />
		</td>
	</tr>
</table>

<div id="dialog"></div>
<div id="form_wrapper" class="form_wrapper"
	style="width: 550px; display: block;">
	<form id="f1" class="register active" style=""
		action="adauga_utilizator_POST.php" method="POST">
		<input type="hidden" name="tipCont"
			value="<?php echo $_GET['type']; ?>" />
		<h3>
			<cufon class="cufon cufon-canvas" alt="Register"
				style="width: 106px; height: 25px;"> <canvas width="122" height="28"
				style="width: 122px; height: 28px; top: -2px; left: 0px;"></canvas>
			<cufontext> Creați utilizator </cufontext></cufon>
		</h3>
		<div class="column">
			<div>
				<label>Nume şi prenume:</label> <input check="true"
					criteria='{type:"string",  minSize: "5", maxSize:"30"}' name="nume"
					type="text" id="nume"> <span class="error">Există o eroare</span>
			</div>
			<div>
				<label>Utilizator:</label> <input id="user" name="user" check="true"
					criteria='{type:"string",minSize:"5",  maxSize:"30"}' type="text">
				<span class="error">Există o eroare</span>
			</div>
			<div>
				<label>Parolă:</label> <input check="true"
					criteria='{type:"string",  minSize: "5", maxSize:"9"}' id="parola"
					name="parola" type="password"> <span class="error">Există o eroare</span>
			</div>
			<?php
			if($_GET['type']=="admin")
			{
				?>
				<input type="hidden" name="idFirma" value="0" /> <input type="hidden"
						name="tipOperator" value="desktop" />

				<?php

			}
			else
			{
				?>
			<div style="margin-left: 50px">

				<div id="idFirma">
					<label>Firma</label> <select name="idFirma">
					<?php
					$result = mysql_query("SELECT id,nume from firma WHERE activa='1'", Aplicatie::getInstance()->getMYSQL()->getResource());
					while($row2 = mysql_fetch_array($result))
					{
						?>
						<option value="<?php echo$row2['id'];?>">
						<?php echo$row2['nume'];?>
						</option>
						<?php
					} ?>
					</select>
				</div>
				<span class="error">Exista o eroare</span></div><?php
			}?>
		</div>

		<div class="bottom">
			<div class="remember"></div>

			<input type="button" class="submit" onclick="checkForm('f1')"
				value="Adaugă utilizator"> <a href="#" rel="login" class="linkform">&nbsp;</a>
			<div class="clear"></div>
		</div>
	</form>

</div>
<div class="clear"></div>

<?php

	Page::showFooter();
